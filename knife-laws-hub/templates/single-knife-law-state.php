<?php
/**
 * Template: Single State Page — /state-knife-laws/{state}/
 */

get_header();

$data = KLH_Post_Type::get_state_data( get_the_ID() );

if ( ! $data ) {
    echo '<div class="klh-state"><p>No state data available.</p></div>';
    get_footer();
    return;
}

$state_name = esc_html( $data['state'] );
$abbr       = esc_html( $data['abbreviation'] );
$verified   = esc_html( $data['last_verified'] );
?>

<div class="klh-state">
    <!-- Breadcrumbs -->
    <nav class="klh-breadcrumbs" aria-label="Breadcrumb">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
        <span class="klh-breadcrumbs__sep" aria-hidden="true">&rsaquo;</span>
        <a href="<?php echo esc_url( get_post_type_archive_link( 'knife_law_state' ) ); ?>">State Knife Laws</a>
        <span class="klh-breadcrumbs__sep" aria-hidden="true">&rsaquo;</span>
        <span aria-current="page"><?php echo $state_name; ?></span>
    </nav>

    <!-- Title + Verified -->
    <header class="klh-state__header">
        <h1><?php echo $state_name; ?> Knife Laws</h1>
        <?php if ( $verified ) : ?>
            <p class="klh-state__verified">Last verified: <time datetime="<?php echo esc_attr( $verified ); ?>"><?php echo $verified; ?></time></p>
        <?php endif; ?>
    </header>

    <!-- Quick Answer Box -->
    <div class="klh-quick-answer">
        <h2 class="klh-quick-answer__title">Quick Answer</h2>
        <ul class="klh-quick-answer__list">
            <li>
                <strong>Open carry:</strong>
                <?php echo KLH_Templates::render_badge( $data['carry']['open_carry']['legal'] ); ?>
            </li>
            <li>
                <strong>Concealed carry:</strong>
                <?php echo KLH_Templates::render_badge( $data['carry']['concealed_carry']['legal'] ); ?>
            </li>
            <li>
                <strong>Blade length limit:</strong>
                <?php
                if ( $data['blade_length']['statewide_limit_inches'] !== null ) {
                    echo esc_html( $data['blade_length']['statewide_limit_inches'] . ' inches' );
                } else {
                    echo '<span class="klh-badge klh-badge--yes">No statewide limit</span>';
                }
                ?>
            </li>
            <li>
                <strong>Switchblade:</strong>
                <?php echo KLH_Templates::render_badge( $data['knife_types']['switchblade']['legal'] ); ?>
            </li>
            <li>
                <strong>Automatic knife:</strong>
                <?php echo KLH_Templates::render_badge( $data['knife_types']['automatic']['legal'] ); ?>
            </li>
            <li>
                <strong>Statewide preemption:</strong>
                <?php echo KLH_Templates::render_badge( $data['preemption']['has_statewide_preemption'] ); ?>
            </li>
        </ul>
    </div>

    <!-- Key Takeaways / Traveler Notes -->
    <?php if ( has_excerpt() || get_the_content() ) : ?>
        <div class="klh-takeaways">
            <h2>Key Takeaways</h2>
            <?php the_content(); ?>
        </div>
    <?php endif; ?>

    <!-- Compare CTA -->
    <div class="klh-state__compare-cta">
        <a href="<?php echo esc_url( home_url( '/knife-laws-compare/?states=' . urlencode( $abbr ) ) ); ?>" class="klh-btn klh-btn--outline">Compare <?php echo $state_name; ?> with another state &rarr;</a>
    </div>

    <!-- Accordion Sections -->
    <div class="klh-state__details">

        <?php
        // --- Carry ---
        ob_start();
        ?>
        <div class="klh-section-content">
            <h4>Open Carry</h4>
            <p><?php echo KLH_Templates::render_badge( $data['carry']['open_carry']['legal'] ); ?></p>
            <?php if ( ! empty( $data['carry']['open_carry']['details'] ) ) : ?>
                <p><?php echo esc_html( $data['carry']['open_carry']['details'] ); ?></p>
            <?php endif; ?>
            <?php echo KLH_Templates::render_statutes( $data['carry']['open_carry']['statutes'] ); ?>

            <h4>Concealed Carry</h4>
            <p><?php echo KLH_Templates::render_badge( $data['carry']['concealed_carry']['legal'] ); ?></p>
            <?php if ( ! empty( $data['carry']['concealed_carry']['details'] ) ) : ?>
                <p><?php echo esc_html( $data['carry']['concealed_carry']['details'] ); ?></p>
            <?php endif; ?>
            <?php echo KLH_Templates::render_statutes( $data['carry']['concealed_carry']['statutes'] ); ?>
        </div>
        <?php
        echo KLH_Templates::render_accordion( 'carry', 'Carry Laws (Open & Concealed)', ob_get_clean(), true );
        ?>

        <?php
        // --- Blade Length ---
        ob_start();
        ?>
        <div class="klh-section-content">
            <?php if ( $data['blade_length']['statewide_limit_inches'] !== null ) : ?>
                <p><strong>Statewide limit:</strong> <?php echo esc_html( $data['blade_length']['statewide_limit_inches'] ); ?> inches</p>
            <?php else : ?>
                <p>No statewide blade length limit.</p>
            <?php endif; ?>
            <?php if ( ! empty( $data['blade_length']['details'] ) ) : ?>
                <p><?php echo esc_html( $data['blade_length']['details'] ); ?></p>
            <?php endif; ?>
            <?php echo KLH_Templates::render_statutes( $data['blade_length']['statutes'] ); ?>
        </div>
        <?php
        echo KLH_Templates::render_accordion( 'blade-length', 'Blade Length & Restrictions', ob_get_clean() );
        ?>

        <?php
        // --- Knife Types ---
        ob_start();
        ?>
        <div class="klh-section-content">
            <?php
            $types = array(
                'switchblade'     => 'Switchblade',
                'automatic'       => 'Automatic Knife',
                'assisted_opening' => 'Assisted Opening',
                'ballistic_knife' => 'Ballistic Knife',
            );
            foreach ( $types as $key => $label ) :
                $type_data = $data['knife_types'][ $key ];
            ?>
                <h4><?php echo esc_html( $label ); ?>: <?php echo KLH_Templates::render_badge( $type_data['legal'] ); ?></h4>
                <?php if ( ! empty( $type_data['details'] ) ) : ?>
                    <p><?php echo esc_html( $type_data['details'] ); ?></p>
                <?php endif; ?>
                <?php echo KLH_Templates::render_statutes( $type_data['statutes'] ); ?>
            <?php endforeach; ?>
        </div>
        <?php
        echo KLH_Templates::render_accordion( 'knife-types', 'Knife Types (Switchblade, Automatic, etc.)', ob_get_clean() );
        ?>

        <?php
        // --- Restricted Locations ---
        ob_start();
        ?>
        <div class="klh-section-content">
            <?php if ( ! empty( $data['restricted_locations']['summary'] ) ) : ?>
                <p><strong>Summary:</strong> <?php echo esc_html( $data['restricted_locations']['summary'] ); ?></p>
            <?php endif; ?>
            <?php if ( ! empty( $data['restricted_locations']['details'] ) ) : ?>
                <p><?php echo esc_html( $data['restricted_locations']['details'] ); ?></p>
            <?php endif; ?>
            <?php echo KLH_Templates::render_statutes( $data['restricted_locations']['statutes'] ); ?>
        </div>
        <?php
        echo KLH_Templates::render_accordion( 'restricted-locations', 'Restricted Locations', ob_get_clean() );
        ?>

        <?php
        // --- Preemption ---
        ob_start();
        ?>
        <div class="klh-section-content">
            <p><?php echo KLH_Templates::render_badge( $data['preemption']['has_statewide_preemption'], 'Statewide Preemption' ); ?></p>
            <?php if ( ! empty( $data['preemption']['details'] ) ) : ?>
                <p><?php echo esc_html( $data['preemption']['details'] ); ?></p>
            <?php endif; ?>
            <?php echo KLH_Templates::render_statutes( $data['preemption']['statutes'] ); ?>
        </div>
        <?php
        echo KLH_Templates::render_accordion( 'preemption', 'Preemption & Local Laws', ob_get_clean() );
        ?>

    </div>

    <!-- Sources Reviewed -->
    <?php
    ob_start();
    ?>
    <div class="klh-section-content">
        <?php if ( ! empty( $data['sources']['primary_official'] ) ) : ?>
            <h4>Official Sources (Tier 1)</h4>
            <ul>
                <?php foreach ( $data['sources']['primary_official'] as $src ) : ?>
                    <li>
                        <?php if ( ! empty( $src['url'] ) ) : ?>
                            <a href="<?php echo esc_url( $src['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $src['title'] ?: $src['url'] ); ?></a>
                        <?php else : ?>
                            <?php echo esc_html( $src['title'] ); ?>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if ( ! empty( $data['sources']['secondary_mirrors'] ) ) : ?>
            <h4>Mirror Sources (Tier 2)</h4>
            <ul>
                <?php foreach ( $data['sources']['secondary_mirrors'] as $src ) : ?>
                    <li>
                        <?php if ( ! empty( $src['url'] ) ) : ?>
                            <a href="<?php echo esc_url( $src['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $src['title'] ?: $src['url'] ); ?></a>
                        <?php else : ?>
                            <?php echo esc_html( $src['title'] ); ?>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    <?php
    echo KLH_Templates::render_accordion( 'sources', 'Sources Reviewed', ob_get_clean() );
    ?>

    <!-- Disclaimer -->
    <div class="klh-disclaimer">
        <p><strong>Disclaimer:</strong> This information is provided for educational purposes only and does not constitute legal advice. Laws change frequently — the statutes linked above were last verified on <?php echo $verified; ?>. Always verify current statutes and consult a qualified attorney for legal guidance specific to your situation.</p>
    </div>

    <!-- Back to Hub -->
    <nav class="klh-state__nav">
        <a href="<?php echo esc_url( get_post_type_archive_link( 'knife_law_state' ) ); ?>">&larr; All State Knife Laws</a>
    </nav>
</div>

<?php
get_footer();
