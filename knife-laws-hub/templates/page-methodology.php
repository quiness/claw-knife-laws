<?php
/**
 * Template: Methodology & Sources — /knife-laws-methodology/
 */

get_header();
?>

<div class="klh-methodology">
    <!-- Breadcrumbs -->
    <nav class="klh-breadcrumbs" aria-label="Breadcrumb">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
        <span class="klh-breadcrumbs__sep" aria-hidden="true">&rsaquo;</span>
        <a href="<?php echo esc_url( get_post_type_archive_link( 'knife_law_state' ) ); ?>">State Knife Laws</a>
        <span class="klh-breadcrumbs__sep" aria-hidden="true">&rsaquo;</span>
        <span aria-current="page">Methodology</span>
    </nav>

    <header class="klh-methodology__header">
        <h1>Methodology &amp; Sources</h1>
        <p>How we research, verify, and maintain knife law data across all 50 states.</p>
    </header>

    <div class="klh-methodology__content">

        <section>
            <h2>Our Approach</h2>
            <p>Every claim on KnifeInformer's State Knife Laws Hub is backed by specific statutory citations. We do not provide legal advice — we compile, summarize, and link to the actual law so you can read it yourself.</p>
        </section>

        <section>
            <h2>Source Hierarchy</h2>

            <h3>Tier 1 — Official State Sources (Preferred)</h3>
            <p>Official state legislature websites and statutory code databases. These are the primary, authoritative source for all claims.</p>

            <h3>Tier 2 — Legal Mirrors (Fallback)</h3>
            <p>When official sources are unavailable (blocked, broken, or difficult to link to directly), we use reputable legal databases:</p>
            <ul>
                <li><strong>Justia</strong> — free legal information and statute mirrors</li>
                <li><strong>Cornell LII</strong> — Legal Information Institute</li>
                <li><strong>FindLaw</strong> — legal information service</li>
            </ul>
            <p>Mirror sources are always labeled as such and are not treated as primary.</p>

            <h3>Tier 3 — Orientation Only</h3>
            <p>Summaries from organizations (such as AKTI) and other knife law guides are used as <strong>orientation only</strong> to identify relevant statutes. They are never cited as primary sources.</p>
        </section>

        <section>
            <h2>Verification Process</h2>
            <ol>
                <li><strong>Identify relevant statutes</strong> using official code search and cross-referencing multiple sources.</li>
                <li><strong>Read the full statute text</strong> — not just summaries — to determine the rule.</li>
                <li><strong>Validate every link</strong> — confirm the URL loads, matches the cited code section, and reflects the current version.</li>
                <li><strong>Record the verification date</strong> — each state page shows the date its data was last verified.</li>
                <li><strong>Flag ambiguity</strong> — when a rule is unclear or context-dependent, we mark it as "Unclear" rather than guessing.</li>
            </ol>
        </section>

        <section>
            <h2>Link Validation Rules</h2>
            <p>For every linked statute, we verify:</p>
            <ul>
                <li>The URL loads without errors (no 403, 404, or redirect loops)</li>
                <li>The content matches the cited code section</li>
                <li>The statute is the current version (not an archived past version)</li>
            </ul>
            <p>If an official URL is blocked or broken, we provide a Justia or LII mirror link and note the limitation.</p>
        </section>

        <section>
            <h2>Handling Uncertainty</h2>
            <p>If a regulation is ambiguous, context-dependent, or requires legal interpretation:</p>
            <ul>
                <li>We store the status as <strong>"Unclear"</strong></li>
                <li>We provide details explaining the ambiguity</li>
                <li>We <strong>never guess</strong> or make assumptions</li>
            </ul>
        </section>

        <section>
            <h2>Update Cycle</h2>
            <ul>
                <li><strong>Quarterly:</strong> automated link health scan</li>
                <li><strong>Annually:</strong> full verification cycle for all 50 states</li>
                <li><strong>As-needed:</strong> when a significant law change is reported or discovered</li>
            </ul>
        </section>

        <section>
            <h2>Schema &amp; Data Model</h2>
            <p>All state data is stored in a normalized JSON schema. This single dataset powers the hub directory, individual state pages, and the comparison tool — ensuring consistency across the site.</p>
        </section>

    </div>

    <!-- Disclaimer -->
    <div class="klh-disclaimer">
        <p><strong>Disclaimer:</strong> This information is provided for educational purposes only and does not constitute legal advice. Always verify current statutes and consult a qualified attorney for legal guidance.</p>
    </div>
</div>

<?php
get_footer();
