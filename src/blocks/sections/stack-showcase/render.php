<?php
defined('ABSPATH') || exit;

$slides = [
	['title' => 'Data Extractie',     'dot' => '#38bdf8', 'text' => 'Wij halen gestructureerde data op uit websites, documenten en systemen. Schoon, geordend en klaar om te gebruiken.'],
	['title' => 'n8n Automatisering', 'dot' => '#a78bfa', 'text' => 'Processen die je tijd kosten draaien voortaan vanzelf. Van leadopvolging tot rapportage, met n8n als motor.'],
	['title' => 'Custom Software',    'dot' => '#f472b6', 'text' => 'Dashboards, portals en tools op maat. Gebouwd precies zoals jij het nodig hebt, niet zoals de markt het aanbiedt.'],
];
?>
<section class="snel-stack-showcase-section <?php echo snel_section_class($attributes); ?> <?php echo snel_section_padding($attributes); ?>"<?php echo snel_section_style($attributes); ?>>
	<div class="px-4 md:px-8">
		<div class="mx-auto w-full max-w-5xl">
			<div class="snel-stack-showcase" data-slides="<?php echo esc_attr(wp_json_encode($slides)); ?>">
				<div class="relative flex w-full items-center justify-center overflow-hidden rounded-xl bg-slate-950 aspect-[3/2]">
					<div class="h-8 w-8 animate-spin rounded-full border-2 border-white/20 border-t-teal-400"></div>
				</div>
			</div>
		</div>
	</div>
</section>
