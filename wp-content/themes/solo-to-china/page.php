<?php
/**
 * Page template for SoloToChina core landing pages.
 *
 * @package SoloToChina
 */

get_header();

$page_id = get_queried_object_id();
$slug    = get_post_field( 'post_name', $page_id );

$core_pages = [
	'survival-kit'      => [
		'title' => 'Survival Kit',
		'copy'  => 'Set up the five systems that make an independent China trip work: money, apps, mobile data, entry documents, and internet access.',
		'share' => true,
		'items' => [
			[
				'id'       => 'payment',
				'number'   => '01',
				'title'    => 'Payment',
				'kicker'   => 'Money that works on arrival',
				'copy'     => 'Prepare mobile payment before departure, then keep a second card and a small cash reserve.',
				'icon'     => 'payment',
				'checklist' => [
					'Install Alipay and WeChat, register with the phone number you will keep during the trip, and complete any identity checks shown in the app.',
					'Link a primary international card and a card from a different issuer. Ask both banks to allow China and app-based transactions.',
					'Test the payment code, know your card PINs, and bring a modest amount of RMB for small vendors or a temporary app failure.',
				],
				'fallback' => 'Keep one physical card separate from your wallet and enough RMB for a meal, local ride, and trip back to your accommodation.',
				'ready'    => 'Both payment apps open, at least one card is verified, and you can still pay if your main phone or bank declines a transaction.',
				'source'   => 'https://english.www.gov.cn/2025special/bizexpatsinchina2025',
				'source_label' => 'Official payment guidance for overseas visitors',
			],
			[
				'id'       => 'essential-apps',
				'number'   => '02',
				'title'    => 'Essential Apps',
				'kicker'   => 'Your pocket travel desk',
				'copy'     => 'Cover five jobs: payment, maps, translation, ride hailing, and intercity transport.',
				'icon'     => 'apps',
				'checklist' => [
					'Install a China-compatible map, an offline translation pack, Alipay or WeChat, a ride-hailing option, and the railway app or your chosen booking provider.',
					'Create accounts and finish passport or phone verification while you can still receive messages from your usual number.',
					'Save your hotel name, address, and first destination in Chinese. Screenshot every booking that you may need without a connection.',
				],
				'fallback' => 'Carry the accommodation address in Chinese, a screenshot of your route, and the booking reference—not only a link inside an app.',
				'ready'    => 'You can show an address, translate a short sentence, call a ride, and retrieve a train or hotel booking while offline.',
				'source'   => 'https://www.12306.cn/en/faq.html',
				'source_label' => 'China Railway 12306 passenger guide',
			],
			[
				'id'       => 'esim',
				'number'   => '03',
				'title'    => 'eSIM & Mobile Data',
				'kicker'   => 'Land with a working connection',
				'copy'     => 'Choose between roaming, a travel eSIM, and a local SIM by checking what your phone—and your itinerary—actually need.',
				'icon'     => 'esim',
				'checklist' => [
					'Confirm that your phone is unlocked and supports eSIM. Check mainland China coverage, activation timing, expiry, hotspot use, top-ups, and the fair-use policy.',
					'Know whether the plan is data-only. A travel eSIM may not provide a mainland Chinese number for calls, SMS, deliveries, or some registrations.',
					'Install the eSIM before flying, save the QR code and instructions offline, label both SIMs clearly, and disable data roaming on the wrong line.',
				],
				'fallback' => 'Keep your home SIM active for essential verification messages if your plan allows it, and note where you can buy a passport-registered local SIM after arrival.',
				'ready'    => 'You know which line supplies data, when it activates, how much data you have, and what you will do if it never connects.',
			],
			[
				'id'       => 'visa',
				'number'   => '04',
				'title'    => 'Visa & Entry',
				'kicker'   => 'Match the rule to your passport and route',
				'copy'     => 'Visa-free entry, a visitor visa, and visa-free transit are different permissions. Confirm the one that fits your exact trip.',
				'icon'     => 'visa',
				'checklist' => [
					'Check the rule for your nationality, passport type, purpose, length of stay, arrival date, and every place you plan to visit.',
					'If relying on visa-free transit, confirm that you are travelling onward to a third country or region through eligible ports and staying inside the permitted area.',
					'Carry your passport, onward or return booking, accommodation details, and any visa or supporting document required for your travel purpose.',
				],
				'fallback' => 'Save the contact details for the Chinese embassy or consulate responsible for your residence and recheck the official rule shortly before departure.',
				'ready'    => 'You can name the exact entry policy you are using and show the itinerary and documents that prove you meet its conditions.',
				'source'   => 'https://en.nia.gov.cn/n147418/n147463/c183412/content.html',
				'source_label' => 'National Immigration Administration entry policies',
			],
			[
				'id'       => 'internet-access',
				'number'   => '05',
				'title'    => 'Internet Access',
				'kicker'   => 'Plan for restricted or unreliable services',
				'copy'     => 'Do not make a critical booking, password, or route depend on one foreign website or one connection method.',
				'icon'     => 'vpn',
				'checklist' => [
					'Confirm which messaging, email, cloud, and work services you need and whether they operate normally on the connection you intend to use.',
					'Download maps, translations, tickets, hotel details, emergency contacts, and password-manager or authentication recovery codes before departure.',
					'Use lawful, reputable connectivity services, keep devices updated, and avoid sensitive transactions on unknown public Wi-Fi.',
				],
				'fallback' => 'Keep essential files on the device, retain a second way to connect, and tell a trusted person how to reach you if your usual messaging app is unavailable.',
				'ready'    => 'Losing access to one app or network would be inconvenient—not trip-ending.',
			],
		],
	],
	'city-guides'       => [
		'title' => 'City Guides',
		'copy'  => 'City hubs for planning where to stay, how to move, and what to do.',
		'share' => true,
		'items' => stc_get_site_collection( 'city-guides' ),
	],
	'attraction-guides' => [
		'title' => 'Attraction Guides',
		'copy'  => 'Ticket timing, passport notes, best seasons, and practical visit planning.',
		'share' => true,
		'items' => stc_get_site_collection( 'attraction-guides' ),
	],
	'planner'           => [
		'title' => 'Build your China trip with AI.',
		'copy'  => 'Choose your destination, dates, and travel style. Trip.Planner turns them into a day-by-day itinerary you can adjust in seconds.',
		'share' => false,
	],
	'tools'             => [
		'title' => 'Tools',
		'copy'  => 'Choose a tool and get the next step.',
		'share' => true,
	],
	'faq'               => [
		'title' => 'FAQ',
		'copy'  => 'Practical answers for the decisions that usually cause first-trip problems—plus the exceptions and fallback plans worth knowing.',
		'share' => true,
		'items' => [
			[
				'category' => 'Entry & documents',
				'title'    => 'Do I need a visa to visit China?',
				'answer'   => [
					'That depends on your nationality, passport type, reason for travel, arrival date, and length of stay. Some passports qualify for time-limited visa-free entry, while other travelers need a visa before departure.',
					'Check the National Immigration Administration, the Chinese embassy or consulate responsible for your place of residence, and your airline before you commit to non-refundable travel. Rules can change and work, study, journalism, and other regulated activities normally require the appropriate permission even when tourist entry is visa-free.',
				],
				'link' => '/survival-kit/#visa', 'link_label' => 'Use the visa and entry checklist',
				'source' => 'https://en.nia.gov.cn/', 'source_label' => 'Check current official entry information',
			],
			[
				'category' => 'Entry & documents',
				'title'    => 'Can I use the 240-hour visa-free transit policy for a round trip?',
				'answer'   => [
					'Usually not for a simple out-and-back journey. Visa-free transit requires an eligible traveler to continue from one country or region, through an eligible part of China, to a different country or region. Your inbound and confirmed onward itinerary must satisfy the policy, and movement is limited to the permitted area.',
					'Do not assume that a connection, open-jaw ticket, or side trip qualifies. Check every flight or train segment, port, date, and destination against the current official policy before ticketing.',
				],
				'link' => '/survival-kit/#visa', 'link_label' => 'Review the entry decision steps',
				'source' => 'https://en.nia.gov.cn/n147418/n147463/c183412/content.html', 'source_label' => 'Read the official transit policy',
			],
			[
				'category' => 'Entry & documents',
				'title'    => 'Which travel documents should I keep available?',
				'answer'   => [
					'Carry the passport used for every booking, your visa if required, onward or return travel, and the name, phone number, and Chinese address of your first accommodation. Keep insurance and key booking details available offline.',
					'Use the same spelling and document number across flights, trains, hotels, and attraction tickets. Save encrypted digital copies separately, but remember that a copy does not replace the original passport when the original is required.',
				],
				'link' => '/survival-kit/#visa', 'link_label' => 'Build your document checklist',
			],
			[
				'category' => 'Money & phone',
				'title'    => 'How can I pay in China with an overseas bank card?',
				'answer'   => [
					'Overseas visitors can register for Alipay or WeChat with a foreign or Chinese mobile number and link supported international cards. The issuing bank still has to approve the connection and each transaction, so one successful card link is not a complete backup plan.',
					'Set up both a primary and secondary method before departure, complete any identity checks, notify your bank, and try a small payment after arrival. Card brands, fees, and transaction limits can vary by product; use the terms shown in the app at the time of payment.',
				],
				'link' => '/survival-kit/#payment', 'link_label' => 'Open the payment setup checklist',
				'source' => 'https://english.www.gov.cn/2025special/bizexpatsinchina2025', 'source_label' => 'Read the official payment guide',
			],
			[
				'category' => 'Money & phone',
				'title'    => 'Should I still carry cash and a physical card?',
				'answer'   => [
					'Yes. Mobile payment is usually the smoothest day-to-day method, but your phone can lose power, an app can ask for reverification, or a bank can decline a transaction. International card acceptance is also uneven outside major hotels, airports, and larger merchants.',
					'Carry a modest RMB reserve in useful denominations and one physical card away from your main wallet. The goal is not to fund the whole trip in cash; it is to cover food, a local ride, or the return to your hotel during a temporary failure.',
				],
				'link' => '/survival-kit/#payment', 'link_label' => 'See the payment fallback plan',
			],
			[
				'category' => 'Money & phone',
				'title'    => 'Is a travel eSIM better than roaming or a local SIM?',
				'answer'   => [
					'A travel eSIM is often convenient for a short trip because you can install it before landing. Roaming may be simpler if your home carrier offers a reasonable package. A local SIM can provide a mainland number and generous data, but normally requires an unlocked phone and passport registration.',
					'Compare mainland coverage, activation timing, expiry, hotspot support, top-up options, and whether the plan is data-only. Keep the QR code offline and do not delete the eSIM until you are sure it can be reinstalled.',
				],
				'link' => '/survival-kit/#esim', 'link_label' => 'Compare the three connection options',
			],
			[
				'category' => 'Apps & internet',
				'title'    => 'Which apps should I install before departure?',
				'answer'   => [
					'Cover the jobs you will perform, not just a generic “top apps” list: mobile payment, a China-compatible map, translation with an offline language pack, ride hailing, intercity transport, and access to your accommodation and attraction bookings.',
					'Create accounts and complete passport or phone checks before you fly. Then save the Chinese address of your hotel, your first route, and booking screenshots offline. One app may handle several jobs, but no single app should hold the only copy of a critical reservation.',
				],
				'link' => '/survival-kit/#essential-apps', 'link_label' => 'Use the essential apps checklist',
			],
			[
				'category' => 'Apps & internet',
				'title'    => 'Can I rely on Google, WhatsApp, and my usual cloud services?',
				'answer'   => [
					'Do not assume every overseas service will work normally on every mainland connection. Availability can differ between local mobile data, hotel Wi-Fi, international roaming, and travel eSIMs, and it can change without notice.',
					'Before departure, download maps, tickets, translations, important files, and recovery codes. Confirm that the connection method you choose supports the services you genuinely need, and keep a second contact method for family or work.',
				],
				'link' => '/survival-kit/#internet-access', 'link_label' => 'Prepare an internet fallback',
			],
			[
				'category' => 'Apps & internet',
				'title'    => 'Will I need a Chinese phone number?',
				'answer'   => [
					'Not for every trip. Foreign numbers can be used for many visitor-facing apps and payment registrations, while a data-only travel eSIM may be enough for maps, translation, and messaging.',
					'A mainland number can still help with delivery calls, some local services, or longer stays. If those matter, compare a passport-registered local SIM with keeping your home number active for verification messages. Make sure your phone supports the SIM combination you plan to use.',
				],
				'link' => '/survival-kit/#esim', 'link_label' => 'Plan your SIM setup',
			],
			[
				'category' => 'Getting around & staying safe',
				'title'    => 'How do I book and board high-speed trains?',
				'answer'   => [
					'Use the official 12306 website or app, a station counter, or a trusted booking provider. Enter your name and passport number exactly as shown in the document. Foreign passengers can use a valid passport for real-name tickets and normally board with the same original passport.',
					'Arrive with time for security and document checks, especially at a large station you do not know. A screenshot or itinerary sheet is useful for reference, but it does not replace the travel document tied to the ticket.',
				],
				'link' => '/planner/', 'link_label' => 'Continue planning the route',
				'source' => 'https://www.12306.cn/en/faq.html', 'source_label' => 'Check the official 12306 passenger guide',
			],
			[
				'category' => 'Getting around & staying safe',
				'title'    => 'Is China manageable for a first-time solo traveler?',
				'answer'   => [
					'For many visitors, yes—but preparation matters more when nobody else can solve a phone, payment, or language problem for you. Share your itinerary, use licensed transport, keep your accommodation address in Chinese, and watch your belongings in busy stations and tourist areas.',
					'Keep separate payment and connection backups. In an immediate emergency, police is 110, ambulance is 120, and fire is 119. Hotel staff, station staff, and your embassy or consulate can also help with non-emergency problems.',
				],
				'link' => '/city-guides/', 'link_label' => 'Browse practical city guides',
			],
			[
				'category' => 'Getting around & staying safe',
				'title'    => 'Do I need to register where I am staying?',
				'answer'   => [
					'Yes. A hotel normally records a foreign guest’s accommodation registration during check-in, so use the passport attached to the booking. If you stay in a private home or other non-hotel accommodation, you or your host generally need to complete registration with the local public security authority within 24 hours.',
					'Ask the host about the process before arrival and keep the registration record if one is issued. Procedures can vary locally, so use the instructions from the authority responsible for the address.',
				],
				'link' => '/city-guides/', 'link_label' => 'Plan your first arrival',
				'source' => 'https://en.nia.gov.cn/n147423/n147478/n147715/c158241/content.html', 'source_label' => 'Read the official accommodation rule',
			],
		],
	],
];
$core_pages = array_merge( $core_pages, stc_static_page_metadata() );

$page                = $core_pages[ $slug ] ?? null;
$guide_landing_slugs = [ 'survival-kit', 'city-guides', 'attraction-guides' ];
$static_page_slugs   = [ 'about', 'contact', 'privacy-policy', 'terms-of-use', 'affiliate-disclosure', 'disclaimer' ];
?>

<main id="main" class="stc-main">
	<?php if ( $page ) : ?>
		<section class="stc-page-hero stc-page-hero--visual stc-page-hero--<?php echo esc_attr( $slug ); ?>">
			<?php if ( in_array( $slug, $static_page_slugs, true ) ) : ?>
				<nav class="stc-static-page__breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'solo-to-china' ); ?>"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'solo-to-china' ); ?></a><span aria-hidden="true">/</span><span aria-current="page"><?php echo esc_html( $page['title'] ); ?></span></nav>
			<?php else : ?>
				<p><?php esc_html_e( 'SoloToChina', 'solo-to-china' ); ?></p>
			<?php endif; ?>
			<h1><?php echo esc_html( $page['title'] ); ?></h1>
			<span><?php echo esc_html( $page['copy'] ); ?></span>
			<?php if ( 'planner' === $slug ) : ?>
				<div class="stc-planner-hero__actions">
					<a class="stc-button stc-planner-hero__cta" href="<?php echo esc_url( stc_get_trip_planner_url() ); ?>" target="_blank" rel="sponsored nofollow noopener noreferrer" data-stc-planner-cta>
						<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m12 2 1.5 5.2L19 9l-5.5 1.8L12 16l-1.5-5.2L5 9l5.5-1.8L12 2Z"/><path d="m19 15 .7 2.3L22 18l-2.3.7L19 21l-.7-2.3L16 18l2.3-.7L19 15Z"/></svg>
						<?php esc_html_e( 'Generate my AI itinerary', 'solo-to-china' ); ?>
						<span aria-hidden="true">&#8594;</span>
					</a>
				</div>
				<ul class="stc-planner-hero__benefits" aria-label="<?php esc_attr_e( 'Planner benefits', 'solo-to-china' ); ?>">
					<li><?php esc_html_e( 'Day-by-day route', 'solo-to-china' ); ?></li>
					<li><?php esc_html_e( 'AI recommendations', 'solo-to-china' ); ?></li>
					<li><?php esc_html_e( 'Easy to adjust', 'solo-to-china' ); ?></li>
				</ul>
				<div class="stc-planner-hero__preview" aria-hidden="true">
					<p>Your AI trip</p>
					<div><strong>8 days in China</strong><span>Ready</span></div>
					<ol>
						<li><span>01</span><div><strong>Beijing</strong><small>Forbidden City &middot; Hutongs</small></div></li>
						<li><span>02</span><div><strong>Xi'an</strong><small>City Wall &middot; Terracotta Army</small></div></li>
						<li><span>03</span><div><strong>Shanghai</strong><small>Old City &middot; The Bund</small></div></li>
					</ol>
				</div>
			<?php elseif ( ! empty( $page['share'] ) ) : ?>
				<div class="stc-page-actions">
					<?php stc_render_share_this_page( array( 'title' => $page['title'], 'description' => $page['copy'] ) ); ?>
				</div>
			<?php endif; ?>
			<?php if ( 'survival-kit' === $slug ) : ?>
				<span class="stc-page-hero__mark" aria-hidden="true">01—05</span>
			<?php elseif ( 'faq' === $slug ) : ?>
				<span class="stc-page-hero__mark" aria-hidden="true">Q / A</span>
			<?php endif; ?>
		</section>

		<div class="stc-page-primary">
		<?php if ( 'survival-kit' === $slug ) : ?>
			<section class="stc-page-section stc-survival-index" aria-labelledby="stc-survival-index-title">
				<div class="stc-survival-index__intro">
					<div>
						<p class="stc-section-kicker"><?php esc_html_e( 'Your pre-departure setup', 'solo-to-china' ); ?></p>
						<h2 id="stc-survival-index-title"><?php esc_html_e( 'Five systems. One calmer arrival.', 'solo-to-china' ); ?></h2>
					</div>
					<p><?php esc_html_e( 'Work through these in order, then keep the fallback for each one. The goal is not a perfect setup—it is avoiding any single point of failure.', 'solo-to-china' ); ?></p>
				</div>
				<nav class="stc-survival-topic-grid" aria-label="<?php esc_attr_e( 'Survival Kit topics', 'solo-to-china' ); ?>">
					<?php foreach ( $page['items'] as $item ) : ?>
						<a class="stc-survival-topic-card" href="#<?php echo esc_attr( $item['id'] ); ?>">
							<span class="stc-survival-topic-card__number"><?php echo esc_html( $item['number'] ); ?></span>
							<?php stc_render_survival_icon( $item['icon'] ); ?>
							<span class="stc-survival-topic-card__content">
								<strong><?php echo esc_html( $item['title'] ); ?></strong>
								<small><?php echo esc_html( $item['kicker'] ); ?></small>
							</span>
							<span class="stc-survival-topic-card__arrow" aria-hidden="true">&#8595;</span>
						</a>
					<?php endforeach; ?>
				</nav>
			</section>

			<section class="stc-page-section stc-survival-playbook" aria-labelledby="stc-survival-playbook-title">
				<header class="stc-survival-playbook__header">
					<div>
						<p class="stc-section-kicker"><?php esc_html_e( 'The field manual', 'solo-to-china' ); ?></p>
						<h2 id="stc-survival-playbook-title"><?php esc_html_e( 'Set it up. Test it. Back it up.', 'solo-to-china' ); ?></h2>
					</div>
					<p><?php esc_html_e( 'App support, fees, connectivity, and entry policies can change. Recheck time-sensitive details with the named provider or official authority shortly before departure.', 'solo-to-china' ); ?></p>
				</header>
				<div class="stc-survival-playbook__list">
					<?php foreach ( $page['items'] as $item ) : ?>
						<article id="<?php echo esc_attr( $item['id'] ); ?>" class="stc-survival-guide-card">
							<header class="stc-survival-guide-card__header">
								<div class="stc-survival-guide-card__identity">
									<span class="stc-survival-guide-card__icon"><?php stc_render_survival_icon( $item['icon'] ); ?></span>
									<span class="stc-survival-guide-card__number"><?php echo esc_html( $item['number'] ); ?></span>
								</div>
								<div>
									<p><?php echo esc_html( $item['kicker'] ); ?></p>
									<h2><?php echo esc_html( $item['title'] ); ?></h2>
									<span class="stc-survival-guide-card__summary"><?php echo esc_html( $item['copy'] ); ?></span>
								</div>
								<a href="#stc-survival-index-title"><?php esc_html_e( 'Back to topics', 'solo-to-china' ); ?> <span aria-hidden="true">&#8593;</span></a>
							</header>
							<div class="stc-survival-guide-card__body">
								<section>
									<h3><?php esc_html_e( 'Before you fly', 'solo-to-china' ); ?></h3>
									<ol class="stc-survival-checklist">
										<?php foreach ( $item['checklist'] as $check ) : ?>
											<li><?php echo esc_html( $check ); ?></li>
										<?php endforeach; ?>
									</ol>
								</section>
								<aside class="stc-survival-fallback">
									<div>
										<h3><?php esc_html_e( 'Carry this fallback', 'solo-to-china' ); ?></h3>
										<p><?php echo esc_html( $item['fallback'] ); ?></p>
									</div>
									<div class="stc-survival-ready">
										<strong><?php esc_html_e( 'You are ready when', 'solo-to-china' ); ?></strong>
										<p><?php echo esc_html( $item['ready'] ); ?></p>
									</div>
									<?php if ( ! empty( $item['source'] ) ) : ?>
										<a class="stc-official-source" href="<?php echo esc_url( $item['source'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $item['source_label'] ); ?> <span aria-hidden="true">&#8599;</span></a>
									<?php endif; ?>
								</aside>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</section>
		<?php elseif ( in_array( $slug, [ 'city-guides', 'attraction-guides' ], true ) ) : ?>
			<?php
			$guide_grid_id    = 'city-guides' === $slug ? 'stc-city-guide-grid' : 'stc-attraction-guide-grid';
			$guide_grid_label = 'city-guides' === $slug ? 'Cities' : 'Attractions';
			$remaining_guides = max( 0, count( $page['items'] ) - 4 );
			?>
			<section class="stc-page-section">
				<div class="stc-guide-grid-shell" data-stc-guide-grid-shell data-stc-guide-label="<?php echo esc_attr( $guide_grid_label ); ?>">
				<div id="<?php echo esc_attr( $guide_grid_id ); ?>" class="stc-card-grid <?php echo esc_attr( 'city-guides' === $slug ? 'stc-card-grid--cities' : 'stc-card-grid--attractions' ); ?>" data-stc-guide-grid>
					<?php foreach ( $page['items'] as $item ) : ?>
						<article class="stc-image-card stc-image-card--<?php echo esc_attr( $item['class'] ); ?>">
							<?php stc_render_guide_card_media( $item['image'], $item['title'] ); ?>
							<?php if ( ! empty( $item['tag'] ) ) : ?>
								<span class="stc-image-card__tag"><?php echo esc_html( $item['tag'] ); ?></span>
							<?php endif; ?>
							<a class="stc-image-card__link" href="<?php echo esc_url( $item['url'] ); ?>">
								<span class="stc-image-card__content">
									<strong><?php echo esc_html( $item['title'] ); ?></strong>
									<span><?php echo esc_html( $item['copy'] ); ?></span>
								</span>
							</a>
						</article>
					<?php endforeach; ?>
				</div>
					<div class="stc-guide-grid-reveal">
						<button type="button" hidden data-stc-guide-reveal aria-controls="<?php echo esc_attr( $guide_grid_id ); ?>" aria-expanded="false">
							<span data-stc-guide-reveal-label><?php echo esc_html( sprintf( '+%d More %s', $remaining_guides, $guide_grid_label ) ); ?></span>
							<svg class="stc-guide-grid-reveal__chevron" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m7 9 5 5 5-5"/></svg>
						</button>
					</div>
				</div>
			</section>
		<?php elseif ( in_array( $slug, $static_page_slugs, true ) ) : ?>
			<article class="stc-static-page stc-static-page--<?php echo esc_attr( $slug ); ?>">
				<div class="stc-static-page__content">
					<?php
					while ( have_posts() ) :
						the_post();
						the_content();
					endwhile;
					?>
				</div>
			</article>
		<?php elseif ( 'planner' === $slug ) : ?>
			<section class="stc-planner stc-planner--page" aria-labelledby="stc-planner-page-title">
				<div class="stc-planner__intro">
					<span class="stc-planner__icon" aria-hidden="true">
						<svg viewBox="0 0 48 48" focusable="false"><path d="M24 5 27.2 16.8 39 20l-11.8 3.2L24 35l-3.2-11.8L9 20l11.8-3.2L24 5Z"/><path d="m39 32 1.4 4.6L45 38l-4.6 1.4L39 44l-1.4-4.6L33 38l4.6-1.4L39 32Z"/></svg>
					</span>
					<div>
						<p class="stc-planner__eyebrow"><?php esc_html_e( 'What AI handles for you', 'solo-to-china' ); ?></p>
						<h2 id="stc-planner-page-title"><?php esc_html_e( 'From trip idea to an editable plan', 'solo-to-china' ); ?></h2>
						<p><?php esc_html_e( 'Set the basics once. AI organizes the route, daily stops, and practical travel flow in one place.', 'solo-to-china' ); ?></p>
					</div>
				</div>
				<ol class="stc-planner__steps">
					<li><span>01</span><strong><?php esc_html_e( 'Add your trip basics', 'solo-to-china' ); ?></strong><small><?php esc_html_e( 'Destination, dates, and travel style.', 'solo-to-china' ); ?></small></li>
					<li><span>02</span><strong><?php esc_html_e( 'Let AI build the route', 'solo-to-china' ); ?></strong><small><?php esc_html_e( 'A realistic day-by-day itinerary.', 'solo-to-china' ); ?></small></li>
					<li><span>03</span><strong><?php esc_html_e( 'Adjust and continue', 'solo-to-china' ); ?></strong><small><?php esc_html_e( 'Refine stops and check travel options.', 'solo-to-china' ); ?></small></li>
				</ol>
				<span class="stc-planner__art" aria-hidden="true"></span>
			</section>
		<?php elseif ( 'tools' === $slug ) : ?>
			<section class="stc-page-section stc-page-section--tools">
				<?php
				if ( shortcode_exists( 'solo_to_china_tools_directory' ) ) {
					echo do_shortcode( '[solo_to_china_tools_directory]' );
				} else {
					echo '<p>' . esc_html__( 'Activate the SoloToChina Tools plugin to use this tool.', 'solo-to-china' ) . '</p>';
				}
				?>
			</section>
		<?php elseif ( 'faq' === $slug ) : ?>
			<?php
			$faq_groups = [];
			foreach ( $page['items'] as $item ) {
				$faq_groups[ $item['category'] ][] = $item;
			}
			?>
			<section class="stc-faq stc-faq--page" aria-labelledby="stc-faq-page-title">
				<div class="stc-faq-page__intro">
					<div>
						<p class="stc-section-kicker"><?php esc_html_e( 'First-trip answers', 'solo-to-china' ); ?></p>
						<h2 id="stc-faq-page-title"><?php esc_html_e( 'Start with the decision you need to make.', 'solo-to-china' ); ?></h2>
					</div>
					<p><?php esc_html_e( 'Each answer includes the condition that changes the advice and the fallback that keeps a small problem from becoming a lost day.', 'solo-to-china' ); ?></p>
				</div>
				<div class="stc-faq-page__layout">
					<nav class="stc-faq-page__topics" aria-label="<?php esc_attr_e( 'FAQ topics', 'solo-to-china' ); ?>">
						<p><?php esc_html_e( 'Jump to', 'solo-to-china' ); ?></p>
						<?php $faq_group_number = 0; ?>
						<?php foreach ( $faq_groups as $category => $items ) : ?>
							<?php $faq_group_number++; ?>
							<a href="#faq-<?php echo esc_attr( sanitize_title( $category ) ); ?>"><span><?php echo esc_html( sprintf( '%02d', $faq_group_number ) ); ?></span><?php echo esc_html( $category ); ?></a>
						<?php endforeach; ?>
					</nav>
					<div class="stc-faq-page__groups">
						<?php $faq_group_number = 0; ?>
						<?php foreach ( $faq_groups as $category => $items ) : ?>
							<?php $faq_group_number++; ?>
							<section id="faq-<?php echo esc_attr( sanitize_title( $category ) ); ?>" class="stc-faq-group" aria-labelledby="faq-group-<?php echo esc_attr( $faq_group_number ); ?>">
								<header>
									<span><?php echo esc_html( sprintf( '%02d', $faq_group_number ) ); ?></span>
									<h2 id="faq-group-<?php echo esc_attr( $faq_group_number ); ?>"><?php echo esc_html( $category ); ?></h2>
								</header>
								<div class="stc-faq-group__items">
									<?php foreach ( $items as $item ) : ?>
										<details>
											<summary><span><?php echo esc_html( $item['title'] ); ?></span><?php stc_render_faq_chevron(); ?></summary>
											<div class="stc-faq__answer">
												<?php foreach ( $item['answer'] as $paragraph ) : ?>
													<p><?php echo esc_html( $paragraph ); ?></p>
												<?php endforeach; ?>
												<div class="stc-faq__answer-actions">
													<a class="stc-faq__answer-link" href="<?php echo esc_url( home_url( $item['link'] ) ); ?>"><?php echo esc_html( $item['link_label'] ); ?> <span aria-hidden="true">&#8594;</span></a>
													<?php if ( ! empty( $item['source'] ) ) : ?>
														<a class="stc-official-source" href="<?php echo esc_url( $item['source'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $item['source_label'] ); ?> <span aria-hidden="true">&#8599;</span></a>
													<?php endif; ?>
												</div>
											</div>
										</details>
									<?php endforeach; ?>
								</div>
							</section>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>
		</div>

		<?php if ( in_array( $slug, $guide_landing_slugs, true ) ) : ?>
			<?php stc_render_core_page_latest_guides( $slug ); ?>
		<?php endif; ?>
	<?php else : ?>
		<section class="stc-content">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<header class="stc-content__header">
					<h1><?php the_title(); ?></h1>
				</header>
				<div class="stc-entry-content">
					<?php the_content(); ?>
				</div>
			<?php endwhile; ?>
		</section>
	<?php endif; ?>
</main>

<?php
get_footer();
