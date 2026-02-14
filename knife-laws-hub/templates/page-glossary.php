<?php
/**
 * Template: Glossary — /knife-laws-glossary/
 */

get_header();
?>

<div class="klh-glossary">
    <!-- Breadcrumbs -->
    <nav class="klh-breadcrumbs" aria-label="Breadcrumb">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
        <span class="klh-breadcrumbs__sep" aria-hidden="true">&rsaquo;</span>
        <a href="<?php echo esc_url( get_post_type_archive_link( 'knife_law_state' ) ); ?>">State Knife Laws</a>
        <span class="klh-breadcrumbs__sep" aria-hidden="true">&rsaquo;</span>
        <span aria-current="page">Glossary</span>
    </nav>

    <header class="klh-glossary__header">
        <h1>Knife Law Glossary</h1>
        <p>Common terms used in knife regulations across the United States.</p>
    </header>

    <div class="klh-glossary__content">

        <dl class="klh-glossary__list">

            <dt id="assisted-opening">Assisted Opening Knife</dt>
            <dd>A folding knife that uses a mechanical mechanism (spring, torsion bar, etc.) to assist the blade's opening once the user has begun to manually open it. Distinct from a fully automatic knife because the user must initiate the opening movement.</dd>

            <dt id="automatic-knife">Automatic Knife</dt>
            <dd>A knife with a blade that opens automatically by pressing a button, switch, or other mechanism. Sometimes used interchangeably with "switchblade" in statutes, though some states distinguish between the two.</dd>

            <dt id="ballistic-knife">Ballistic Knife</dt>
            <dd>A knife with a detachable blade that can be propelled or ejected from the handle as a projectile. Banned under federal law (15 U.S.C. &sect; 1245) and most state laws.</dd>

            <dt id="blade-length">Blade Length</dt>
            <dd>The measured length of the cutting edge of a knife blade. Many states define restrictions based on blade length, though measurement methods can vary by jurisdiction.</dd>

            <dt id="bowie-knife">Bowie Knife</dt>
            <dd>A large sheath knife with a clip-point blade. Some state statutes specifically mention bowie knives, though definitions vary.</dd>

            <dt id="concealed-carry">Concealed Carry</dt>
            <dd>Carrying a knife hidden from ordinary observation — for example, in a pocket, under clothing, or in a bag. Many states have different rules for concealed vs. open carry of knives.</dd>

            <dt id="dagger">Dagger</dt>
            <dd>A knife with a symmetrical, double-edged blade designed primarily for stabbing. Many states restrict or regulate daggers specifically.</dd>

            <dt id="dirk">Dirk</dt>
            <dd>A long, straight-bladed dagger. Often mentioned alongside "dagger" in statutes. Some jurisdictions use "dirk" broadly to include any fixed-blade knife capable of causing serious injury.</dd>

            <dt id="gravity-knife">Gravity Knife</dt>
            <dd>A knife with a blade that opens by the force of gravity or centrifugal force. The legal definition varies significantly by state and has been subject to controversy.</dd>

            <dt id="open-carry">Open Carry</dt>
            <dd>Carrying a knife in plain view — for example, in a visible sheath on a belt. Generally subject to fewer restrictions than concealed carry, but rules vary by state.</dd>

            <dt id="preemption">Preemption (Statewide)</dt>
            <dd>A state law that prevents local governments (cities, counties) from enacting knife restrictions stricter than state law. States with preemption provide more uniform rules. Without preemption, local ordinances may be more restrictive.</dd>

            <dt id="stiletto">Stiletto</dt>
            <dd>A knife with a long, slender blade designed primarily for thrusting. Some states specifically restrict stilettos in their statutes.</dd>

            <dt id="switchblade">Switchblade</dt>
            <dd>A knife with a blade that opens automatically by pressing a button or spring mechanism in the handle. Federal law (15 U.S.C. &sect; 1241-1245) restricts interstate commerce of switchblades, but possession and carry laws are set by each state.</dd>

        </dl>

    </div>

    <!-- Disclaimer -->
    <div class="klh-disclaimer">
        <p><strong>Disclaimer:</strong> These definitions are for general reference only. Statutory definitions may differ by state. Always refer to specific state statutes for legally binding definitions.</p>
    </div>
</div>

<?php
get_footer();
