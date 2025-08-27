<?php

$cta_text = get_post_meta($post->ID, 'innovative_solutions_cta_text', true);
$video_url = get_post_meta($post->ID, 'innovative_solutions_video_url', true);
?>

<table class="form-table">
    <tr>
        <th><label for="innovative_solutions_cta_text">CTA Text:</label></th>
        <td>
            <input type="text" name="innovative_solutions_cta_text" id="innovative_solutions_cta_text" 
                   value="<?php echo esc_attr($cta_text); ?>" class="regular-text">
        </td>
    </tr>
    <tr>
        <th><label for="innovative_solutions_video_url">Video Link:</label></th>
        <td>
            <input type="url" name="innovative_solutions_video_url" id="innovative_solutions_video_url" 
                   value="<?php echo esc_url($video_url); ?>" class="regular-text">
        </td>
    </tr>
</table>

<?php wp_nonce_field('innovative_solutions_nonce', 'innovative_solutions_nonce'); ?>
