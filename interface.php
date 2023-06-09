<?php $ip_addresses = $this->get_ip_addresses(); ?>
<section id="blacklist-plugin" class="wp-core-ui wrap">
    <h1 class="wp-heading-inline">
        <?= __('IP Blacklist Settings') ?>
    </h1>
    <table class="wp-list-table widefat fixed striped table-view-list">
        <thead>
        <tr>
            <th><?= __('IP Address') ?></th>
            <th><?= __('Actions') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($ip_addresses as $ip_address): ?>
            <tr>
                <td><?php echo $ip_address; ?></td>
                <td>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="delete" value="<?= $ip_address; ?>">
                        <input type="submit" value="<?= __('Delete') ?>" class="delete" class="button">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($ip_addresses)): ?>
            <tr>
                <td colspan="2"><?= __('No IP addresses have been added to the blacklist yet.') ?></td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
    <div class="tablenav bottom">
        <form method="POST">
            <td><input type="text" name="ip_address" value="<?= (empty($ip_addresses)) ? $_SERVER['REMOTE_ADDR'] : '' ?>" placeholder="<?= __('IP Address') ?>"></td>
            <td><input type="submit" name="add" value="<?= __('Add') ?>" class="button"></td>
        </form>
    </div>
</section>
