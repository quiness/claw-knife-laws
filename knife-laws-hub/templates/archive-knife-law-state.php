<?php
/**
 * Template: Hub / Directory page — /state-knife-laws/
 *
 * Lists all 50 states with badges, search, and filters.
 */

get_header();

$states = KLH_Post_Type::get_all_states();
?>

<div class="klh-hub">
    <!-- Hero -->
    <header class="klh-hub__hero">
        <h1>State Knife Laws — All 50 States</h1>
        <p class="klh-hub__subtitle">Verified statutes and plain-English summaries for every US state. Search, filter, or compare.</p>
    </header>

    <!-- Search + Filters -->
    <div class="klh-hub__controls">
        <div class="klh-hub__search">
            <label for="klh-search" class="screen-reader-text">Search states</label>
            <input type="text" id="klh-search" class="klh-search__input" placeholder="Search by state name or abbreviation&hellip;" autocomplete="off">
        </div>

        <div class="klh-hub__filters">
            <button class="klh-filter-toggle" aria-expanded="false" aria-controls="klh-filters-panel">
                Filters <span class="klh-filter-toggle__count" hidden></span>
            </button>
            <div id="klh-filters-panel" class="klh-filters-panel" hidden>
                <fieldset class="klh-filter-group">
                    <legend>Preemption</legend>
                    <label><input type="radio" name="f_preemption" value=""> Any</label>
                    <label><input type="radio" name="f_preemption" value="true"> Yes</label>
                    <label><input type="radio" name="f_preemption" value="false"> No</label>
                    <label><input type="radio" name="f_preemption" value="null"> Unknown</label>
                </fieldset>
                <fieldset class="klh-filter-group">
                    <legend>Open Carry</legend>
                    <label><input type="radio" name="f_open_carry" value=""> Any</label>
                    <label><input type="radio" name="f_open_carry" value="true"> Yes</label>
                    <label><input type="radio" name="f_open_carry" value="false"> No</label>
                    <label><input type="radio" name="f_open_carry" value="null"> Unknown</label>
                </fieldset>
                <fieldset class="klh-filter-group">
                    <legend>Concealed Carry</legend>
                    <label><input type="radio" name="f_concealed_carry" value=""> Any</label>
                    <label><input type="radio" name="f_concealed_carry" value="true"> Yes</label>
                    <label><input type="radio" name="f_concealed_carry" value="false"> No</label>
                    <label><input type="radio" name="f_concealed_carry" value="null"> Unknown</label>
                </fieldset>
                <fieldset class="klh-filter-group">
                    <legend>Switchblade</legend>
                    <label><input type="radio" name="f_switchblade" value=""> Any</label>
                    <label><input type="radio" name="f_switchblade" value="true"> Legal</label>
                    <label><input type="radio" name="f_switchblade" value="false"> Illegal</label>
                    <label><input type="radio" name="f_switchblade" value="null"> Unknown</label>
                </fieldset>
                <fieldset class="klh-filter-group">
                    <legend>Automatic Knife</legend>
                    <label><input type="radio" name="f_automatic" value=""> Any</label>
                    <label><input type="radio" name="f_automatic" value="true"> Legal</label>
                    <label><input type="radio" name="f_automatic" value="false"> Illegal</label>
                    <label><input type="radio" name="f_automatic" value="null"> Unknown</label>
                </fieldset>
                <fieldset class="klh-filter-group">
                    <legend>Blade Length Limit</legend>
                    <label><input type="radio" name="f_blade_limit" value=""> Any</label>
                    <label><input type="radio" name="f_blade_limit" value="yes"> Has limit</label>
                    <label><input type="radio" name="f_blade_limit" value="no"> No limit</label>
                </fieldset>
                <button class="klh-filters__clear" type="button">Clear all filters</button>
            </div>
        </div>
    </div>

    <!-- Compare CTA -->
    <div class="klh-hub__compare-cta">
        <a href="<?php echo esc_url( home_url( '/knife-laws-compare/' ) ); ?>" class="klh-btn klh-btn--outline">Compare Two States &rarr;</a>
    </div>

    <!-- State Grid -->
    <div class="klh-hub__grid" id="klh-state-grid">
        <?php if ( empty( $states ) ) : ?>
            <p class="klh-hub__empty">No state data available yet. States are being added.</p>
        <?php else : ?>
            <?php foreach ( $states as $state ) : ?>
                <a href="<?php echo esc_url( $state['permalink'] ); ?>" class="klh-state-card"
                   data-state="<?php echo esc_attr( strtolower( $state['state'] ) ); ?>"
                   data-abbr="<?php echo esc_attr( strtolower( $state['abbreviation'] ) ); ?>"
                   data-preemption="<?php echo esc_attr( klh_json_tri( $state['preemption']['has_statewide_preemption'] ) ); ?>"
                   data-open-carry="<?php echo esc_attr( klh_json_tri( $state['carry']['open_carry']['legal'] ) ); ?>"
                   data-concealed-carry="<?php echo esc_attr( klh_json_tri( $state['carry']['concealed_carry']['legal'] ) ); ?>"
                   data-switchblade="<?php echo esc_attr( klh_json_tri( $state['knife_types']['switchblade']['legal'] ) ); ?>"
                   data-automatic="<?php echo esc_attr( klh_json_tri( $state['knife_types']['automatic']['legal'] ) ); ?>"
                   data-blade-limit="<?php echo esc_attr( $state['blade_length']['statewide_limit_inches'] !== null ? 'yes' : 'no' ); ?>">

                    <div class="klh-state-card__header">
                        <h2 class="klh-state-card__name"><?php echo esc_html( $state['state'] ); ?></h2>
                        <span class="klh-state-card__abbr"><?php echo esc_html( $state['abbreviation'] ); ?></span>
                    </div>

                    <div class="klh-state-card__badges">
                        <?php echo KLH_Templates::render_badge( $state['preemption']['has_statewide_preemption'], 'Preemption' ); ?>
                        <?php echo KLH_Templates::render_badge( $state['knife_types']['switchblade']['legal'], 'Switchblade' ); ?>
                        <?php echo KLH_Templates::render_badge( $state['knife_types']['automatic']['legal'], 'Auto' ); ?>
                    </div>

                    <?php if ( ! empty( $state['last_verified'] ) ) : ?>
                        <div class="klh-state-card__verified">
                            Verified <?php echo esc_html( $state['last_verified'] ); ?>
                        </div>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- No results message (hidden by default) -->
    <p class="klh-hub__no-results" id="klh-no-results" hidden>No states match your search or filters.</p>

    <!-- Disclaimer -->
    <div class="klh-disclaimer">
        <p><strong>Disclaimer:</strong> This information is provided for educational purposes only and does not constitute legal advice. Laws change frequently. Always verify current statutes and consult a qualified attorney for legal guidance specific to your situation.</p>
    </div>
</div>

<?php
get_footer();

/**
 * Helper to convert tri-state to a data attribute value.
 */
function self_json_tri( $val ) { // fallback
    return klh_json_tri( $val );
}
function klh_json_tri( $val ) {
    if ( $val === true ) return 'true';
    if ( $val === false ) return 'false';
    return 'null';
}
