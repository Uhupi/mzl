# 🔍 API CORS Debugging Guide

## Problem
Getting CORS error with 301 redirect on `http://mzl.test/api.php?endpoint=team`

## Debugging Steps

### 1. Check What URL the Server Redirects To

Open browser DevTools (F12) and check the Network tab:

1. Open http://mzl.test/api.php?endpoint=team in your browser
2. Look at the Network tab
3. Find the request and check:
   - **Initial request URL** (what you requested)
   - **Response Status** (should show 301 redirect)
   - **Response Headers** → Look for "Location" header (where it redirects to)
   - **Redirected URL** (the final destination after redirect)

### 2. Common Redirect Issues

**Issue: HTTP → HTTPS Redirect**
```
Requested: http://mzl.test/api.php
Redirects to: https://mzl.test/api.php
Solution: Update .env to use https://
```

**Issue: Missing/Extra www**
```
Requested: http://mzl.test/api.php
Redirects to: http://www.mzl.test/api.php
Solution: Update .env to match the redirect URL
```

**Issue: Directory Redirect**
```
Requested: http://mzl.test/api.php
Redirects to: http://mzl.test/api.php/
Solution: Update .env to add trailing slash
```

### 3. Fix Options

#### Option A: Update API URL to Match Redirect
If the browser shows a redirect to a different URL, update `.env`:

```env
# Example if it redirects to https
VITE_API_BASE=https://mzl.test/api.php

# Example if it redirects to www
VITE_API_BASE=http://www.mzl.test/api.php
```

#### Option B: Check Server Configuration
If using Apache/PHP:
- Ensure `.htaccess` is in place (we created one)
- Check if mod_rewrite is enabled
- Verify no redirect rules in .htaccess

If using Nginx:
- Check if there are redirect rules in config
- Ensure `api.php` is not being redirected

#### Option C: Test Direct Access
```bash
# Test with curl to see actual headers
curl -I -v http://mzl.test/api.php?endpoint=team

# Follow redirects
curl -L -I -v http://mzl.test/api.php?endpoint=team
```

### 4. Check .env Configuration

Current setting in `.env`:
```
VITE_API_BASE=http://mzl.test/api.php
```

**What to check:**
- Protocol (http:// vs https://)
- Domain (mzl.test vs www.mzl.test vs something else)
- Path (api.php vs other variations)
- Trailing slash (with or without /)

### 5. Test with Curl

Run these commands to debug:

```bash
# Check if api.php responds
curl -v http://mzl.test/api.php?endpoint=team

# Check CORS headers in response
curl -v http://mzl.test/api.php?endpoint=team | head -20

# Follow redirects to see final URL
curl -L -v http://mzl.test/api.php?endpoint=team
```

### 6. Browser Console Check

Open DevTools (F12) → Console tab and look for detailed CORS errors:

```
Access to XMLHttpRequest at 'http://mzl.test/api.php?endpoint=team' 
from origin 'http://localhost:5173' has been blocked by CORS policy:
Response to preflight request doesn't pass access control checks:
...
```

This tells you exactly what's being blocked.

### 7. Common Solutions

#### Solution 1: Update .env
```env
VITE_API_BASE=https://mzl.test/api.php
```
Then restart dev server: `npm run dev`

#### Solution 2: Check Server Logs
```bash
# PHP built-in server logs show errors
# Apache logs usually in /var/log/apache2/
# Check error_log and access_log for the 301 redirects
```

#### Solution 3: Direct Test URL
Try visiting this in browser:
```
http://mzl.test/api.php?endpoint=team
```
If you see JSON, API works. If you get redirected, note the redirect URL and update `.env`.

## Quick Checklist

- [ ] Opened DevTools Network tab
- [ ] Found the api.php request
- [ ] Checked if there's a 301 redirect
- [ ] Noted the "Location" header (where it redirects)
- [ ] Updated `.env` to match the actual working URL
- [ ] Restarted dev server: `npm run dev`
- [ ] Tested again in browser

## Still Having Issues?

Try these:

1. **Clear browser cache** (Ctrl+Shift+Delete)
2. **Hard refresh** (Ctrl+Shift+R)
3. **Check if mzl.test resolves** 
   ```bash
   ping mzl.test
   nslookup mzl.test
   ```
4. **Verify api.php is accessible**
   ```bash
   curl http://mzl.test/api.php?endpoint=team
   ```

## Debug Output Needed

If still stuck, please provide:
- What URL shows in Browser DevTools Network tab for the failed request?
- What is the "Location" header value (if there's a redirect)?
- Output of: `curl -v http://mzl.test/api.php?endpoint=team`
