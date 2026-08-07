# Evidence-File Preview Bottom Sheet Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the centered-dialog evidence-file preview popup on the student achievement detail page with an animated dark bottom sheet, keeping the same download/close functionality.

**Architecture:** Single Blade view (`resources/views/siswa/achievements/show.blade.php`) already contains the trigger buttons, the popup markup, and an inline `<script>` that opens/closes it. This plan replaces only the popup markup + script block (lines 76–157) with a bottom-sheet variant, then rebuilds the committed frontend asset bundle so the new Tailwind utility classes actually ship to production. No PHP/controller changes.

**Tech Stack:** Laravel Blade, Tailwind CSS v4 (via `@tailwindcss/vite`), vanilla JS (no new dependencies). Frontend assets are committed straight into git at `public_html/rumah-prestasi/build/` — there is no build step on the server, so any Blade change touching Tailwind classes requires `npm run build` and committing the regenerated output *in the same commit* as the Blade change.

## Global Constraints

- No new JS/CSS dependencies — vanilla JS + existing Tailwind setup only (per spec).
- No changes to `app/Http/Controllers/AchievementFileController.php` — `Content-Disposition: inline` stays as-is (per spec).
- No cross-file gallery navigation (next/prev between evidence files) and no image zoom/pinch — explicitly out of scope (per spec).
- No swipe/drag-to-dismiss gesture — close only via backdrop tap, close button, or Escape (per spec).
- These element IDs must be preserved exactly, because they are the JS/HTML contract already used by the trigger buttons above this block: `image-preview-modal`, `image-preview-panel`, `image-preview-name`, `image-preview-download`, `image-preview-close`, `image-preview-img`, `image-preview-frame`.
- Every commit that changes a Tailwind class used anywhere in the touched Blade file must be immediately followed (same commit) by a regenerated `public_html/rumah-prestasi/build/` — this was the root cause of a real production bug earlier ("frame tidak muncul") and must not recur.

---

### Task 1: Replace the popup markup and script with a bottom-sheet variant

**Files:**
- Modify: `core-rumah-prestasi/resources/views/siswa/achievements/show.blade.php:76-157`

**Interfaces:**
- Consumes: trigger buttons elsewhere in the same file already set `data-preview-type` (`"image"` or `"file"`), `data-preview-src`, `data-preview-name` on elements with class `file-preview-trigger` — this task's script reads those same three `dataset` keys, unchanged.
- Produces: same public DOM contract as before (element IDs listed in Global Constraints) — nothing outside this block depends on anything new.

- [ ] **Step 1: Confirm the baseline (old markup) is present before editing**

Run:
```bash
cd /Users/rafael/Downloads/smasga-rumah-prestasi
grep -F 'items-center justify-center bg-navy-950/80 backdrop-blur-sm px-4' core-rumah-prestasi/resources/views/siswa/achievements/show.blade.php
```
Expected: one matching line printed (the current centered-dialog backdrop). If this prints nothing, someone already changed this block — stop and re-read the file before continuing.

- [ ] **Step 2: Replace lines 76–157 with the bottom-sheet markup and script**

Replace this exact block (`old_string`):

```blade
    <div id="image-preview-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-navy-950/80 backdrop-blur-sm px-4" role="dialog" aria-modal="true" aria-labelledby="image-preview-name">
        <div id="image-preview-panel" class="w-full max-w-sm">
            <div class="flex items-center justify-between gap-2 mb-2">
                <div id="image-preview-name" class="text-xs font-semibold text-white/80 truncate"></div>
                <div class="flex items-center gap-2 shrink-0">
                    <a id="image-preview-download" href="#" download class="w-9 h-9 rounded-full bg-white/15 flex items-center justify-center active:scale-95 transition-transform">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/></svg>
                    </a>
                    <button type="button" id="image-preview-close" class="w-9 h-9 rounded-full bg-white/15 flex items-center justify-center active:scale-95 transition-transform">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
            </div>
            <div class="rounded-2xl overflow-hidden bg-slate-900">
                <img id="image-preview-img" src="" alt="" class="w-full max-h-[70vh] object-contain">
                <iframe id="image-preview-frame" src="" class="w-full h-[75vh] hidden" title="Pratinjau berkas"></iframe>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var modal = document.getElementById('image-preview-modal');
            if (!modal || modal.dataset.bound === '1') return;
            modal.dataset.bound = '1';

            var panel = document.getElementById('image-preview-panel');
            var img = document.getElementById('image-preview-img');
            var frame = document.getElementById('image-preview-frame');
            var name = document.getElementById('image-preview-name');
            var downloadBtn = document.getElementById('image-preview-download');
            var closeBtn = document.getElementById('image-preview-close');

            function openModal(type, src, filename) {
                name.textContent = filename;
                downloadBtn.href = src;
                downloadBtn.setAttribute('download', filename);

                if (type === 'image') {
                    panel.classList.remove('max-w-2xl');
                    panel.classList.add('max-w-sm');
                    img.src = src;
                    img.alt = filename;
                    img.classList.remove('hidden');
                    frame.classList.add('hidden');
                    frame.src = 'about:blank';
                } else {
                    panel.classList.remove('max-w-sm');
                    panel.classList.add('max-w-2xl');
                    frame.src = src;
                    frame.classList.remove('hidden');
                    img.classList.add('hidden');
                    img.removeAttribute('src');
                }

                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                img.removeAttribute('src');
                frame.src = 'about:blank';
            }

            document.querySelectorAll('.file-preview-trigger').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    openModal(btn.dataset.previewType, btn.dataset.previewSrc, btn.dataset.previewName);
                });
            });

            closeBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', function (e) {
                if (e.target === modal) closeModal();
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
            });
        })();
    </script>
```

With this (`new_string`):

```blade
    <div id="image-preview-modal" class="fixed inset-0 z-[100] hidden items-end justify-center bg-navy-950/70 backdrop-blur-sm opacity-0 transition-opacity duration-200" role="dialog" aria-modal="true" aria-labelledby="image-preview-name">
        <div id="image-preview-panel" class="w-full max-w-md mx-auto rounded-t-3xl bg-navy-950 p-4 translate-y-full transition-transform duration-[250ms] ease-out">
            <div class="flex items-center justify-between gap-2 mb-2">
                <div id="image-preview-name" class="text-xs font-semibold text-white/80 truncate"></div>
                <div class="flex items-center gap-2 shrink-0">
                    <a id="image-preview-download" href="#" download class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center active:scale-95 transition-transform">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/></svg>
                    </a>
                    <button type="button" id="image-preview-close" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center active:scale-95 transition-transform">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
            </div>
            <div class="rounded-2xl overflow-hidden bg-slate-900">
                <img id="image-preview-img" src="" alt="" class="w-full max-h-[75vh] object-contain">
                <iframe id="image-preview-frame" src="about:blank" class="w-full h-[80vh] hidden" title="Pratinjau berkas"></iframe>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var modal = document.getElementById('image-preview-modal');
            if (!modal || modal.dataset.bound === '1') return;
            modal.dataset.bound = '1';

            var panel = document.getElementById('image-preview-panel');
            var img = document.getElementById('image-preview-img');
            var frame = document.getElementById('image-preview-frame');
            var name = document.getElementById('image-preview-name');
            var downloadBtn = document.getElementById('image-preview-download');
            var closeBtn = document.getElementById('image-preview-close');
            var closeTimer = null;

            function openModal(type, src, filename) {
                clearTimeout(closeTimer);
                name.textContent = filename;
                downloadBtn.href = src;
                downloadBtn.setAttribute('download', filename);

                if (type === 'image') {
                    img.src = src;
                    img.alt = filename;
                    img.classList.remove('hidden');
                    frame.classList.add('hidden');
                    frame.src = 'about:blank';
                } else {
                    frame.src = src;
                    frame.classList.remove('hidden');
                    img.classList.add('hidden');
                    img.removeAttribute('src');
                }

                modal.classList.remove('hidden');
                modal.classList.add('flex');

                requestAnimationFrame(function () {
                    requestAnimationFrame(function () {
                        modal.classList.remove('opacity-0');
                        modal.classList.add('opacity-100');
                        panel.classList.remove('translate-y-full');
                        panel.classList.add('translate-y-0');
                    });
                });
            }

            function closeModal() {
                modal.classList.remove('opacity-100');
                modal.classList.add('opacity-0');
                panel.classList.remove('translate-y-0');
                panel.classList.add('translate-y-full');

                closeTimer = setTimeout(function () {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    img.removeAttribute('src');
                    frame.src = 'about:blank';
                }, 250);
            }

            document.querySelectorAll('.file-preview-trigger').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    openModal(btn.dataset.previewType, btn.dataset.previewSrc, btn.dataset.previewName);
                });
            });

            closeBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', function (e) {
                if (e.target === modal) closeModal();
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
            });
        })();
    </script>
```

Notes on why this code is shaped this way:
- The double `requestAnimationFrame` in `openModal` is required: the browser must paint the closed state (`opacity-0`/`translate-y-full`) *after* `hidden` is removed, before the "open" classes are applied — otherwise the browser coalesces both class changes into a single paint and the transition never visibly plays.
- `closeModal`'s `setTimeout(..., 250)` must stay equal to the panel's `duration-[250ms]` class — it defers `hidden`/`src` cleanup until the slide-down animation has actually finished, so the sheet doesn't visually snap away mid-animation. If a later change adjusts one, adjust the other.
- The `max-w-sm`/`max-w-2xl` width-swapping logic from the old script is removed entirely: the bottom sheet uses one constant width (`max-w-md mx-auto`, matching the app's own `max-w-md` content column defined in `resources/views/layouts/siswa.blade.php:14`) for both file types, differing only by media height (`75vh` image / `80vh` iframe) per the approved spec.

- [ ] **Step 3: Verify the replacement landed correctly**

Run:
```bash
cd /Users/rafael/Downloads/smasga-rumah-prestasi
grep -F 'items-center justify-center bg-navy-950/80 backdrop-blur-sm px-4' core-rumah-prestasi/resources/views/siswa/achievements/show.blade.php
echo "exit code: $?"
grep -F 'items-end justify-center bg-navy-950/70' core-rumah-prestasi/resources/views/siswa/achievements/show.blade.php
grep -F 'translate-y-full' core-rumah-prestasi/resources/views/siswa/achievements/show.blade.php
grep -F 'rounded-t-3xl' core-rumah-prestasi/resources/views/siswa/achievements/show.blade.php
```
Expected: the first `grep` finds nothing (`exit code: 1`); the other three each print at least one matching line.

- [ ] **Step 4: Compile-check the Blade file (no DB/auth needed)**

Run:
```bash
cd /Users/rafael/Downloads/smasga-rumah-prestasi/core-rumah-prestasi
php artisan view:clear
php artisan view:cache
```
Expected: both commands finish with `INFO` success messages and no PHP errors. `view:cache` compiles every Blade view in the app, including this one — a mismatched `@if`/`@endif` or broken PHP expression will throw here immediately. If it errors, re-check the block pasted in Step 2 for a stray Blade directive.

Run `php artisan view:clear` again afterward so the compiled-view cache doesn't mask edits in later manual testing:
```bash
php artisan view:clear
```

Do not commit yet — Task 2 must land in the same commit as this change.

---

### Task 2: Rebuild and commit the frontend asset bundle together with the Blade change

**Files:**
- Modify (generated, do not hand-edit): `public_html/rumah-prestasi/build/manifest.json`
- Delete (generated): whichever `public_html/rumah-prestasi/build/assets/app-*.css` file is currently referenced by the manifest before this task runs
- Create (generated): a new `public_html/rumah-prestasi/build/assets/app-*.css` with a fresh content hash

**Interfaces:**
- Consumes: the exact Blade file content produced by Task 1 (Tailwind scans it at build time — if Task 1 isn't done first, the new utility classes won't be in the output).
- Produces: a `build/assets/app-*.css` file that Task 3 will confirm is the one actually served by the live site.

- [ ] **Step 1: Run the production build**

Run:
```bash
cd /Users/rafael/Downloads/smasga-rumah-prestasi/core-rumah-prestasi
npm run build
```
Expected: output ends with `✓ built in <N>ms` and lists a `../public_html/rumah-prestasi/build/assets/app-<newhash>.css` line. Note the new hash — you'll need it in Step 2.

- [ ] **Step 2: Verify the new CSS actually contains the bottom-sheet classes**

Run (replace `<newhash>` with the value from Step 1):
```bash
cd /Users/rafael/Downloads/smasga-rumah-prestasi
CSS=public_html/rumah-prestasi/build/assets/app-<newhash>.css
grep -Fc 'translate-y-full' "$CSS"
grep -Fc '250ms' "$CSS"
grep -Fc 'rounded-t-3xl' "$CSS" || grep -Fc '.rounded-t-3xl' "$CSS"
```
Expected: each command prints a count of `1` or more (not `0`). If any prints `0`, Task 1's edit didn't land before this build ran — re-run Step 1 of this task after double-checking Task 1 Step 3.

- [ ] **Step 3: Confirm which files changed and stage exactly those**

Run:
```bash
cd /Users/rafael/Downloads/smasga-rumah-prestasi
git status --short
```
Expected to see exactly:
```
 M core-rumah-prestasi/resources/views/siswa/achievements/show.blade.php
 D public_html/rumah-prestasi/build/assets/app-<oldhash>.css
?? public_html/rumah-prestasi/build/assets/app-<newhash>.css
 M public_html/rumah-prestasi/build/manifest.json
```
(JS asset hashes should be unchanged — this task touched no JS-managed files, only inline Blade `<script>`, which Vite doesn't fingerprint.) If any other file shows as modified, stop and check `git diff` on it before staging — do not sweep unrelated changes into this commit.

Stage and commit exactly those four paths:
```bash
git add core-rumah-prestasi/resources/views/siswa/achievements/show.blade.php \
  public_html/rumah-prestasi/build/manifest.json
git add public_html/rumah-prestasi/build/assets/app-*.css
git status --short
```
Expected: staged list matches the four paths above (old CSS staged as deleted, new CSS staged as added).

- [ ] **Step 4: Commit and push**

Run:
```bash
git commit -m "$(cat <<'EOF'
Redesign evidence-file preview as an animated bottom sheet

Replaces the centered-dialog popup on the student achievement page with
a dark, bottom-anchored sheet that slides up with a fade, per the
approved design in docs/superpowers/specs/2026-08-07-evidence-file-preview-bottom-sheet-design.md.
Rebuilds the committed frontend bundle in the same commit so the new
Tailwind classes ship to production immediately.
EOF
)"
git push origin Shared-Hosting
```
Expected: commit succeeds, push shows the branch advancing (e.g. `<oldsha>..<newsha>  Shared-Hosting -> Shared-Hosting`).

---

### Task 3: Verify the redesign live in the browser

**Files:** none (verification only — no code changes)

**Interfaces:**
- Consumes: the deployed result of Task 2's push (production must have picked up the new commit — deployment is external to this repo; see note below).

- [ ] **Step 1: Confirm production is serving the new CSS**

Using Claude in Chrome (or any browser), navigate to `https://rumahprestasi.cafevisual.com/login`, sign in as a student (NIS `12874`), open any achievement detail page, then check which CSS file is loaded:
```js
document.querySelector('link[rel="stylesheet"][href*="app-"]').href
```
Expected: the hash in this URL matches the `<newhash>` from Task 2 Step 1 — **not** any previously-seen hash (e.g. `app-BAzmcgJf.css` or `app-DElogkg8.css`, both stale). If it still shows an old hash, the push from Task 2 has not been deployed yet — wait for whatever deploy mechanism this project uses (confirm with the project owner if unknown) and re-check before continuing; do not proceed to Step 2 against stale CSS.

- [ ] **Step 2: Click an image evidence file and verify the sheet**

Click a row under "Berkas Bukti" for an image file. Verify, visually and via screenshot:
- The sheet slides up from the bottom edge of the screen with a visible fade-in of the dark backdrop (not an instant snap).
- The sheet's top corners are rounded, it spans the full width of the content column, and it is anchored to the bottom of the viewport (not centered).
- The header row inside the sheet shows the filename (truncated if long), a download button, and a close button.
- The image is visible, correctly scaled (not stretched/cropped oddly), roughly filling up to ~75% of viewport height.

- [ ] **Step 3: Click a non-image (PDF) evidence file and verify the sheet**

If the test achievement has no PDF evidence file, submit a temporary one via `siswa/prestasi/tambah` (any small PDF works — clean it up afterward via the same account, or leave it as it doesn't affect other students' data). Click that file's row and verify:
- Same sheet chrome (backdrop fade, slide-up, header) as the image case.
- The PDF renders inside the sheet (an embedded PDF viewer), filling roughly 80% of viewport height, not a download prompt.

- [ ] **Step 4: Verify all three close paths, each with the reverse animation**

For each of the following, confirm the sheet slides back down and the backdrop fades out (not an instant disappearance), and that a repeat open/close cycle still works cleanly afterward:
- Click the close (X) button.
- Click on the dark backdrop area outside the sheet.
- Press `Escape` on the keyboard.

- [ ] **Step 5: Verify the download button and check for console/network issues**

Click the download button while the sheet is open for both an image and the PDF file; confirm each downloads the correct original file (correct filename, correct content) rather than navigating away from the page. Then, using the browser's console and network tools:
```js
// after opening then closing an image preview:
document.getElementById('image-preview-frame').src
```
Expected: `"about:blank"` — never the current page URL (this was a real bug in the previous implementation: assigning `''` to an iframe's `src` makes the browser resolve it to the current document and reload the whole page inside the hidden iframe). Also confirm no JavaScript errors appear in the console during the whole open/close/download flow.

- [ ] **Step 6: Verify on a narrow mobile viewport**

Resize the browser window (or use device emulation) to a narrow width such as 390×844 (a common phone size) and repeat Step 2 and Step 4's backdrop-tap close. Confirm the sheet still spans the full width, the header buttons remain tappable (not clipped), and the media area doesn't overflow the visible sheet height.

If any step in this task fails, do not mark this task complete — go back to Task 1 or Task 2 depending on where the defect lives, fix it, and re-run Task 2 Step 1–4 (rebuild, commit, push) before re-attempting this task from Step 1.
