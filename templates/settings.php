<h1>Fallback</h1>

<form method="post" novalidate="novalidate">
    <table class="form-table" role="presentation">
        <tr>
            <th scope="row"><label for="production_host">Production URL</label></th>

            <td>
                <input name="production_host" type="text" value="<?= $settings->production_host ?>" class="regular-text">
                <p class="description" id="tagline-description">The production URL with https:// included. Example: https://production.com</p>
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="download">Download assets</label></th>

            <td>
                <input name="download" type="checkbox" value="true" class="regular-text" <?= $settings->download ? 'checked' : '' ?>>
                <p class="description" id="tagline-description">Download the assets to it's relative file path file once it has loaded for faster response times</p>
            </td>
        </tr>
    </table>

    <p class="submit">
        <input type="submit" name="submit" id="submit" class="button button-primary" value="<?= __('Save Changes') ?>">
    </p>
</form>
