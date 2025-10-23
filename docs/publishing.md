# Publishing the numanrki/whmcs-license Package

To publish this package to GitHub and Packagist, follow these steps in your terminal from the package directory (c:/Users/numan/Documents/packagist/whmcs-license). Note: Your provided GitHub URL has a typo ("licnese" instead of "license"). Use the correct one: https://github.com/numanrki/whmcs-license.git. If the repository doesn't exist, create it on GitHub first.

1. Initialize Git repository:
   ```
   git init
   ```

2. Add all files:
   ```
   git add .
   ```

3. Commit the changes:
   ```
   git commit -m "feat: initial release"
   ```

4. Rename the branch to main (if needed):
   ```
   git branch -M main
   ```

5. Add the remote origin (use your correct GitHub URL):
   ```
   git remote add origin https://github.com/numanrki/whmcs-license.git
   ```

6. Push to GitHub:
   ```
   git push -u origin main
   ```

7. Create a tag for the version:
   ```
   git tag v1.0.0
   ```

8. Push the tag:
   ```
   git push origin v1.0.0
   ```

9. Submit to Packagist:
   - Go to https://packagist.org/
   - Log in (create an account if needed)
   - Click "Submit" and enter your GitHub repository URL: https://github.com/numanrki/whmcs-license
   - Packagist will detect the composer.json and set it up as numanrki/whmcs-license.

After these steps, your package will be available via Composer. Ensure your GitHub repository is public for Packagist to access it.