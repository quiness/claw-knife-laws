<?php
/**
 * Template: Compare Tool — /knife-laws-compare/
 *
 * State selection and comparison rendering are handled client-side via JS
 * using the /klh/v1/compare REST endpoint.
 */

get_header();

$all_states = KLH_Post_Type::get_all_states();
$state_options = array();
foreach ( $all_states as $s ) {
    $state_options[] = array(
        'abbr' => $s['abbreviation'],
        'name' => $s['state'],
    );
}
?>

<div class="klh-compare">
    <!-- Breadcrumbs -->
    <nav class="klh-breadcrumbs" aria-label="Breadcrumb">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
        <span class="klh-breadcrumbs__sep" aria-hidden="true">&rsaquo;</span>
        <a href="<?php echo esc_url( get_post_type_archive_link( 'knife_law_state' ) ); ?>">State Knife Laws</a>
        <span class="klh-breadcrumbs__sep" aria-hidden="true">&rsaquo;</span>
        <span aria-current="page">Compare</span>
    </nav>

    <header class="klh-compare__header">
        <h1>Compare State Knife Laws</h1>
        <p>Select two states to see a side-by-side comparison of their knife regulations, with statute citations.</p>
    </header>

    <!-- State Selectors -->
    <form class="klh-compare__form" id="klh-compare-form">
        <div class="klh-compare__selectors">
            <div class="klh-compare__select-group">
                <label for="klh-state-a">State A</label>
                <select id="klh-state-a" name="state_a" required>
                    <option value="">Select a state&hellip;</option>
                    <?php foreach ( $state_options as $opt ) : ?>
                        <option value="<?php echo esc_attr( $opt['abbr'] ); ?>"><?php echo esc_html( $opt['name'] ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="klh-compare__vs" aria-hidden="true">vs</div>
            <div class="klh-compare__select-group">
                <label for="klh-state-b">State B</label>
                <select id="klh-state-b" name="state_b" required>
                    <option value="">Select a state&hellip;</option>
                    <?php foreach ( $state_options as $opt ) : ?>
                        <option value="<?php echo esc_attr( $opt['abbr'] ); ?>"><?php echo esc_html( $opt['name'] ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button type="submit" class="klh-btn klh-btn--primary" id="klh-compare-btn">Compare</button>
    </form>

    <!-- Results Container (populated by JS) -->
    <div class="klh-compare__results" id="klh-compare-results" hidden>
        <div class="klh-compare__loading" id="klh-compare-loading" hidden>
            <p>Loading comparison&hellip;</p>
        </div>
        <div id="klh-compare-table-wrap"></div>
    </div>

    <!-- Error Container -->
    <div class="klh-compare__error" id="klh-compare-error" hidden></div>

    <!-- Disclaimer -->
    <div class="klh-disclaimer">
        <p><strong>Disclaimer:</strong> This comparison is for educational purposes only and does not constitute legal advice. Always verify current statutes and consult a qualified attorney.</p>
    </div>
</div>

<script>
/**
 * Inline state data to avoid extra REST call for select population.
 */
window.klhStateOptions = <?php echo wp_json_encode( $state_options ); ?>;
</script>

<?php
get_footer();
