<?php
/**
 * Attraction data for first-phase ticket tool UI.
 *
 * @package SoloToChinaTools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function stc_tools_get_attractions() {
	return [
		[
			'slug'          => 'forbidden-city',
			'name'          => 'Forbidden City',
			'city'          => 'Beijing',
			'booking_note'  => 'Booking required',
			'passport_note' => 'Passport usually required',
			'best_time'     => 'Oct-Apr',
			'booking_lead_days' => 7,
		],
		[
			'slug'          => 'great-wall-mutianyu',
			'name'          => 'Great Wall Mutianyu',
			'city'          => 'Beijing',
			'booking_note'  => 'Advance planning recommended',
			'passport_note' => 'Bring your passport',
			'best_time'     => 'Apr-Oct',
			'booking_lead_days' => 5,
		],
		[
			'slug'          => 'terracotta-warriors',
			'name'          => 'Terracotta Warriors',
			'city'          => "Xi'an",
			'booking_note'  => 'Booking recommended',
			'passport_note' => 'Bring your passport',
			'best_time'     => 'Mar-May',
			'booking_lead_days' => 3,
		],
		[
			'slug'          => 'zhangjiajie-national-forest-park',
			'name'          => 'Zhangjiajie National Forest Park',
			'city'          => 'Zhangjiajie',
			'booking_note'  => 'Peak season planning recommended',
			'passport_note' => 'Bring your passport',
			'best_time'     => 'Apr-Nov',
			'booking_lead_days' => 7,
		],
		[
			'slug'          => 'west-lake',
			'name'          => 'West Lake',
			'city'          => 'Hangzhou',
			'booking_note'  => 'Plan ahead for busy periods',
			'passport_note' => 'Bring your passport',
			'best_time'     => 'Mar-May',
			'booking_lead_days' => 3,
		],
		[
			'slug'          => 'shanghai-disney-resort',
			'name'          => 'Shanghai Disney Resort',
			'city'          => 'Shanghai',
			'booking_note'  => 'Booking required',
			'passport_note' => 'Passport usually required',
			'best_time'     => 'Mar-May',
			'booking_lead_days' => 7,
		],
		[
			'slug'          => 'summer-palace',
			'name'          => 'Summer Palace',
			'city'          => 'Beijing',
			'booking_note'  => 'Advance planning recommended',
			'passport_note' => 'Bring your passport',
			'best_time'     => 'Apr-Oct',
			'booking_lead_days' => 5,
		],
		[
			'slug'          => 'chengdu-panda-base',
			'name'          => 'Chengdu Research Base of Giant Panda Breeding',
			'city'          => 'Chengdu',
			'booking_note'  => 'Morning visit planning recommended',
			'passport_note' => 'Bring your passport',
			'best_time'     => 'Mar-May',
			'booking_lead_days' => 5,
		],
		[
			'slug'          => 'canton-tower',
			'name'          => 'Canton Tower',
			'city'          => 'Guangzhou',
			'booking_note'  => 'Evening slots can fill early',
			'passport_note' => 'Bring your passport',
			'best_time'     => 'Oct-Dec',
			'booking_lead_days' => 3,
		],
		[
			'slug'          => 'yu-garden',
			'name'          => 'Yu Garden',
			'city'          => 'Shanghai',
			'booking_note'  => 'Plan ahead around holidays',
			'passport_note' => 'Bring your passport',
			'best_time'     => 'Mar-May',
			'booking_lead_days' => 3,
		],
	];
}
