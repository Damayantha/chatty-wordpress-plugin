# Chatty WordPress Plugin

Official WordPress plugin for [Chatty](https://chatty.personaliai.com) — adds the Chatty AI chatbot widget to any WordPress site with no code editing required.

## Download

Grab the latest installable zip from the [Releases page](https://github.com/Damayantha/chatty-wordpress-plugin/releases/latest).

## Install

1. In your WordPress admin, go to **Plugins → Add New → Upload Plugin**
2. Choose the downloaded `chatty-widget.zip`
3. Click **Install Now**, then **Activate**
4. Go to **Settings → Chatty Widget** and paste your Bot ID (find it in your [Chatty dashboard](https://chatty.personaliai.com/dashboard) → your bot → Embed & Integrate)
5. Save — the widget now appears on your site

## Development

The plugin source lives in `chatty-widget/`:

- `chatty-widget.php` — main plugin file (settings page + `wp_footer` output)
- `readme.txt` — WordPress.org plugin-directory-format readme
- `uninstall.php` — cleans up plugin options on uninstall

To package a new release zip:

```bash
cd chatty-wordpress-plugin
python -c "
import zipfile, os
with zipfile.ZipFile('chatty-widget.zip', 'w', zipfile.ZIP_DEFLATED) as zf:
    for root, dirs, files in os.walk('chatty-widget'):
        for f in files:
            path = os.path.join(root, f)
            zf.write(path, path)
"
```

## License

GPLv2 or later — see `chatty-widget/readme.txt`.
