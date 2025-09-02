## Grid Tour Creator

### Overview
Grid Tour Creator is a lightweight PHP (CodeIgniter 3) web app for composing a clean 2×3 photo grid with rounded tiles, customizable center labels (R1–R3), left/right corner names, background color, and size presets. You can export a high‑quality PNG directly in the browser (client‑side canvas) or via an optional server render powered by PHP GD.

No build step is required: the frontend uses Tailwind via CDN and vanilla JavaScript with an HTML5 canvas.

### Key Features
- 2 columns × 3 rows grid with consistent spacing and rounded corners
- Drag‑and‑drop reordering of the six image slots
- Per‑image non‑destructive transforms: zoom and pan (double‑click to edit)
- Left/right corner names with bold styling and outline
- Three centered labels (R1, R2, R3) with adjustable sizes
- Preset output sizes: 1440×2160, 1080×1920, 2048×3072
- Background color picker
- Local persistence via `localStorage` so your work survives page reloads
- Optional server‑side rendering (PNG) using PHP GD

### Tech Stack
- Backend: PHP CodeIgniter 3
- Server rendering: PHP GD (imagecreatetruecolor, imagecopyresampled, imagettftext)
- Frontend: Vanilla JavaScript, HTML `<canvas>`, Tailwind CSS via CDN

### Repository Structure
- `index.php`: CodeIgniter front controller and framework bootstrap
- `application/controllers/Collage.php`: Main controller (renders view and handles server render)
- `application/views/collage_editor.php`: Single‑page editor UI and canvas logic
- `application/config/routes.php`: Routing (default route set to the collage editor)
- `system/`: CodeIgniter 3 framework core

### Routing
- Default route → `collage` → `Collage::index` → renders `collage_editor.php`
- Server render endpoint → `POST /collage/generate` → `Collage::generate`

### Requirements
- PHP 7.4+ (PHP 8.x supported)
- PHP extensions: `gd` (for server rendering), `mbstring`
- A web server (Apache, Nginx) or local stack (Laragon, XAMPP, WAMP)
- Optional fonts for server rendering (see Fonts below)

### Quick Start
1) Place the project directory under your web server document root.
   - Example (Windows + Laragon): `C:\laragon\www\grid-tour-creator`
2) Ensure PHP GD is enabled if you plan to use server rendering.
3) Visit the app in your browser:
   - `http://localhost/grid-tour-creator/`

Optional configuration (recommended):
- Open `application/config/config.php` and set `base_url` to your local URL.

### Using the Editor
1) Upload up to 6 images (click a tile to pick a file). You can drag tiles to reorder.
2) Enter left and right names. These are rendered in bold with a soft outline for readability.
3) Enter labels R1, R2, R3 and adjust their size sliders as needed.
4) Pick a background color and choose one of the size presets.
5) Double‑click any image tile to open the per‑image editor:
   - Scroll to zoom (0.5× to 4×)
   - Drag to pan
   - Reset to revert
   - Save to persist the transform for that slot
6) Click “Unduh PNG” to download the collage as a PNG generated client‑side.

Note: The UI text is in Indonesian, but functionality is straightforward and can be localized if needed.

### Client–Server Rendering
The app supports an optional server‑side render path that mirrors the client layout and draws the final PNG using PHP GD. This is disabled in the UI by default (button commented in the view). To enable:
1) Open `application/views/collage_editor.php`
2) Find the commented button with id `saveServer` and uncomment it
3) Ensure the `gd` extension is enabled in PHP

When invoked, the editor sends `multipart/form-data` to `POST /collage/generate`. The server reads parameters, performs layout math identical to the client (2×3 grid, gap 28, radius 18), applies image transforms, renders labels and names with TTF fonts, and streams a PNG response.

### API Contract (Server Render)
- Endpoint: `POST /collage/generate`
- Content-Type: `multipart/form-data`
- Scalar fields:
  - `width`, `height`: output dimensions (e.g., 1440 and 2160)
  - `bg`: background color as hex string (3‑ or 6‑digit, e.g., `#111827`)
  - `leftName`, `rightName`: corner names
  - `r1`, `r2`, `r3`: center labels
- Per‑image transform fields (for i = 1..6):
  - `img{i}_scale`: scale multiplier (0.5–4.0)
  - `img{i}_ox`, `img{i}_oy`: integer pixel offsets for panning
- File fields (optional):
  - `img1`..`img6`: images (JPEG/PNG/GIF). Any slot can be omitted.

Response:
- `Content-Type: image/png`
- `Content-Disposition: inline; filename="collage.png"`
- Body: PNG stream

### Layout and Rendering Details
- Grid: 2 columns × 3 rows
- Spacing: `gap = 28`
- Tile corner radius: `18`
- Tile size (W×H): computed from output size minus gaps
- Center labels are horizontally centered at each row’s midpoint; client allows per‑label size tuning, while the server uses a fixed proportion relative to the tile height.

### Persistence
The app persists editor state in `localStorage` under the key `grid-tour-creator-v1`, including:
- Names and labels (`leftName`, `rightName`, `r1`, `r2`, `r3`)
- Label sizes (`r1Size`, `r2Size`, `r3Size`)
- Background `bg` and selected `size`
- `images`: per‑slot data URLs for fast restore
- `transforms`: per‑slot `{ scale, offsetX, offsetY }`

You can clear all saved data by clicking “Hapus data” (Clear data) in the header.

### Fonts (Server)
Server text rendering (names and labels) uses TTF fonts resolved in this order:
1) Inter (place `Inter-Regular.ttf` and `Inter-Bold.ttf` in `application/third_party/fonts/`)
2) Windows Arial (`C:\\Windows\\Fonts\\arial.ttf`, `arialbd.ttf`)
3) DejaVu (Linux: `/usr/share/fonts/truetype/dejavu/DejaVuSans*.ttf`)

If Inter is not provided, the server automatically falls back to Arial or DejaVu when available.

### Security Notes
- Clickjacking protection: `X-Frame-Options: SAMEORIGIN` is set in the controller constructor.
- Upload handling is fail‑soft: missing or invalid images are skipped; only in‑memory processing is performed.
- Accepted server image types: JPEG, PNG, GIF.

### Troubleshooting
- Blank or partial PNG on server render:
  - Ensure PHP `gd` is enabled and sufficient `memory_limit` is configured for your output size
  - Verify file uploads are not blocked by `post_max_size` / `upload_max_filesize`
- Unexpected fonts on server:
  - Provide Inter TTFs under `application/third_party/fonts/`, or rely on OS fonts
- Client preview looks different from server image:
  - The client supports per‑label size sliders; the server currently uses a fixed label size proportion

### Development Notes
- No build step required; assets load via CDN
- Keep custom business logic within controllers or libraries per CodeIgniter conventions
- Use `site_url()` in views when generating links to controller methods

### License
This project is built on CodeIgniter 3, which is MIT‑licensed. See `license.txt` for details.

### Acknowledgements
- CodeIgniter 3 framework
- Tailwind CSS

