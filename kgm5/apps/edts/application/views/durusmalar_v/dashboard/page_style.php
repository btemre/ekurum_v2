<link href="<?php echo base_url('assets/css/custom.css'); ?>?v=<?php echo asset_ver(); ?>" rel="stylesheet" type="text/css" />
<style>
.dashboard-hero { padding: 1rem 0 1.5rem; border-bottom: 1px solid var(--bs-gray-200); }
.dashboard-stat-card { border-radius: 0.65rem; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: box-shadow 0.2s ease; }
.dashboard-stat-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,0.08); }
.dashboard-stat-card .stat-value { font-size: 1.75rem; font-weight: 700; line-height: 1.2; }
.dashboard-stat-card .stat-label { font-size: 0.9rem; color: var(--bs-gray-600); }
.dashboard-lawyer-list { max-height: 12rem; overflow-y: auto; }
.dashboard-section-title { font-size: 1.1rem; font-weight: 600; margin-bottom: 0.75rem; }
#kt_content_durusmalar_list thead th,
#kt_content_durusmalar_list tbody td { padding: 0.35rem 0.5rem; line-height: 1.3; vertical-align: middle; }
#kt_content_durusmalar_list { font-size: 0.9rem; white-space: nowrap; }
.ai-asistan-premium { border-radius: 0.5rem; background: linear-gradient(135deg, #f1faff 0%, #e8f4fc 100%); border: 1px solid rgba(0,158,247,0.2); }
.btn-ai { font-weight: 700; letter-spacing: 0.02em; padding: 0.5rem 1rem; border-radius: 0.5rem; }
.btn-ai.btn-light-primary { background: linear-gradient(180deg, #f1faff 0%, #e8f4fc 100%); border: 1px solid #009ef7; color: #009ef7; }
.btn-ai.btn-light-info { background: linear-gradient(180deg, #f1faff 0%, #e8f6fc 100%); border: 1px solid #00bcd4; color: #00a3b8; }
/* Premium AI ile Ara butonu - turuncu */
.btn-ai-premium {
	background: linear-gradient(135deg, #fd7e14 0%, #e8590c 50%, #d9480f 100%);
	color: #fff !important;
	font-weight: 700;
	letter-spacing: 0.03em;
	border: none;
	border-radius: 50px;
	padding: 0.5rem 1.25rem;
	box-shadow: 0 4px 14px rgba(253, 126, 20, 0.45), 0 1px 3px rgba(0,0,0,0.12);
	transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.btn-ai-premium:hover {
	color: #fff !important;
	transform: translateY(-1px);
	box-shadow: 0 6px 20px rgba(253, 126, 20, 0.5), 0 2px 6px rgba(0,0,0,0.15);
}
.btn-ai-premium:active {
	transform: translateY(0);
	box-shadow: 0 2px 10px rgba(253, 126, 20, 0.4);
}
.btn-ai-premium .bi-stars { color: #000 !important; }
</style>
