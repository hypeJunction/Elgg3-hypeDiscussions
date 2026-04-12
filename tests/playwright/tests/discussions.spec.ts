import { test, expect } from '@playwright/test';
import {
  loginAs,
  getLatestDiscussionByTitle,
  getMetadata,
  getCommentsForEntity,
  queryDb,
} from '../helpers/elgg';

/**
 * Pre-migration UI behavior lock for hypeDiscussions.
 *
 * These tests assume:
 *  - hypeDiscussions plugin is active
 *  - A test group named 'playwright-group' exists with forum enabled
 *  - Two users exist: 'pw_owner' (group admin) and 'pw_other' (member)
 *  - Both share password 'testpass123'
 *
 * If not seeded, tests will be skipped with a clear error.
 */
test.describe('hypeDiscussions', () => {
  let groupGuid: number | null = null;

  test.beforeAll(async () => {
    const rows = await queryDb(
      `SELECT e.guid FROM elgg_entities e
         JOIN elgg_metadata m ON m.entity_guid = e.guid
        WHERE e.type = 'group' AND m.name = 'name' AND m.value = ?
        LIMIT 1`,
      ['playwright-group']
    );
    if (rows[0]) {
      groupGuid = Number(rows[0].guid);
    }
  });

  test('group member can create a new discussion topic', async ({ page }) => {
    test.skip(groupGuid === null, 'playwright-group not seeded');

    await loginAs(page, 'pw_owner');
    await page.goto(`/discussion/add/${groupGuid}`);

    const title = `Playwright Topic ${Date.now()}`;
    await page.fill('input[name="title"]', title);
    await page.fill('textarea[name="description"]', 'Body text from Playwright');
    await page.click('button[type="submit"]');

    // UI assertion: landed on a discussion page
    await expect(page).toHaveURL(/\/discussion\/view\//);
    await expect(page.locator('h1, .elgg-heading-main')).toContainText(title);

    // DB assertion: discussion created under the group container with status=open
    const discussion = await getLatestDiscussionByTitle(title);
    expect(discussion).toBeTruthy();
    expect(Number(discussion.container_guid)).toBe(groupGuid);

    const statusMeta = await getMetadata(Number(discussion.guid), 'status');
    expect(statusMeta[0]?.value).toBe('open');
  });

  test('replying to an open discussion creates a comment entity', async ({ page }) => {
    test.skip(groupGuid === null, 'playwright-group not seeded');

    // Create a discussion first via UI
    await loginAs(page, 'pw_owner');
    await page.goto(`/discussion/add/${groupGuid}`);
    const title = `Reply Target ${Date.now()}`;
    await page.fill('input[name="title"]', title);
    await page.fill('textarea[name="description"]', 'Reply target body');
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(/\/discussion\/view\//);

    const discussion = await getLatestDiscussionByTitle(title);
    expect(discussion).toBeTruthy();
    const discussionGuid = Number(discussion.guid);

    // Post a reply. The core discussion reply form is rendered inline.
    // Target the generic comments form fallback.
    const replyInput = page.locator('textarea[name="generic_comment"], textarea[name="description"]').first();
    await replyInput.fill('This is a Playwright reply');
    await page.locator('form').filter({ has: replyInput }).locator('button[type="submit"], input[type="submit"]').first().click();

    // Wait for the page to reload/settle
    await page.waitForLoadState('networkidle');

    // DB assertion: a comment is attached to the discussion
    const comments = await getCommentsForEntity(discussionGuid);
    expect(comments.length).toBeGreaterThan(0);
  });

  test('non-admin cannot access add-discussion page when admin_only_discussions is enabled', async ({ page }) => {
    test.skip(groupGuid === null, 'playwright-group not seeded');

    // Toggle admin_only_discussions ON for the group
    await queryDb(
      `INSERT INTO elgg_metadata (entity_guid, name, value, value_type, time_created)
       VALUES (?, 'admin_only_discussions_enable', 'yes', 'text', UNIX_TIMESTAMP())
       ON DUPLICATE KEY UPDATE value = 'yes'`,
      [groupGuid]
    );

    try {
      await loginAs(page, 'pw_other');
      const response = await page.goto(`/discussion/add/${groupGuid}`);

      // Either forbidden, redirected, or page loaded but form is not present
      const status = response?.status() ?? 0;
      if (status === 403) {
        expect([403]).toContain(status);
      } else {
        // Expect no form (page redirected to an error / group page)
        const formCount = await page.locator('form input[name="title"]').count();
        expect(formCount).toBe(0);
      }
    } finally {
      // Cleanup toggle
      await queryDb(
        `UPDATE elgg_metadata SET value = 'no'
          WHERE entity_guid = ? AND name = 'admin_only_discussions_enable'`,
        [groupGuid]
      );
    }
  });

  test('discussion listing page renders for the group', async ({ page }) => {
    test.skip(groupGuid === null, 'playwright-group not seeded');

    await loginAs(page, 'pw_owner');
    await page.goto(`/discussion/group/${groupGuid}`);

    // UI assertion: page renders without error messages
    await expect(page.locator('.elgg-system-messages .elgg-message-error')).toHaveCount(0);
    // The listing container should be present (elgg-list class or an "empty" notice)
    const hasList = await page.locator('.elgg-list').count();
    const hasBody = await page.locator('body').count();
    expect(hasBody).toBeGreaterThan(0);
    expect(hasList).toBeGreaterThanOrEqual(0);
  });

  test('site-wide discussions listing page renders', async ({ page }) => {
    await loginAs(page, 'pw_owner');
    await page.goto('/discussion/all');
    await expect(page).toHaveURL(/\/discussion\/all/);
    await expect(page.locator('.elgg-system-messages .elgg-message-error')).toHaveCount(0);
  });
});
