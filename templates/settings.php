<h1>Fallback</h1>

<form method="post" novalidate="novalidate">
    <table class="form-table" role="presentation">
        <tr>
            <th scope="row"><label for="production_host">Production URL</label></th>

            <td class="<?= isset($errors['production_host']) ? 'form-invalid' : '' ?>">
                <input name="production_host" type="text" value="<?= esc_html($settings->production_host) ?>" class="regular-text">

                <?php if (isset($errors['production_host'])) : ?>
                    <p class="error-message"><?= esc_html($errors['production_host']) ?></p>
                <?php endif; ?>

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
        <input type="submit" name="submit" id="submit" class="button button-primary" value="<?= __('Save Changes', 'wlafp') ?>">
    </p>
</form>
