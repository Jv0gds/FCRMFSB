<?php
// pages/client_dashboard.php

// session_start() is called in index.php, so we don't need it here.
// db.php is also included in the original file, let's keep it.
// Assuming db.php is included via index.php or we should include it here.
// Let's ensure it's included for standalone safety.
include_once 'db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.html');
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch the most recent lead to represent the current main project
$stmt_latest = $pdo->prepare("SELECT * FROM leads WHERE created_by_user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt_latest->execute([$user_id]);
$latest_lead = $stmt_latest->fetch();

// Fetch the assigned sales representative for the latest lead
$representative = null;
if ($latest_lead && !empty($latest_lead['assigned_to_user_id'])) {
    $stmt_rep = $pdo->prepare("SELECT full_name, email, phone, avatar_url FROM users WHERE id = ?");
    $stmt_rep->execute([$latest_lead['assigned_to_user_id']]);
    $representative = $stmt_rep->fetch();
}

// Fetch all leads for the "Recent Activity" log
$stmt_all = $pdo->prepare("SELECT company_name, created_at, status FROM leads WHERE created_by_user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt_all->execute([$user_id]);
$all_leads = $stmt_all->fetchAll();

// --- Business Logic for Stepper ---
$stages = [t('step_submit_application'), t('step_assign_consultant'), t('step_needs_communication'), t('step_solution_formulation'), t('step_contract_signing')];
$current_stage_index = -1; // -1 means no project yet

if ($latest_lead) {
    // In a real application, you'd have a more robust mapping from DB status to these stages.
    $status_map = [
        'New' => 0,
        'Assigned' => 1,
        'In Progress' => 2,
        'Contacted' => 2,
        'Qualified' => 2,
        'Proposal' => 3,
        'Won' => 4,
        'Lost' => 4, // or handle differently
    ];
    $current_status = $latest_lead['status'] ?? 'New';
    $current_stage_index = $status_map[$current_status] ?? 0;
}

?>

<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        grid-gap: 24px;
    }
    .grid-col-span-2 {
        grid-column: span 2;
    }
    .card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
        padding: 24px;
        display: flex;
        flex-direction: column;
    }
    .welcome-banner {
        background: linear-gradient(135deg, var(--accent-color) 0%, #0056b3 100%);
        color: white;
    }
    .welcome-banner h2 { margin-bottom: 8px; }
    .welcome-banner p { margin-bottom: 24px; opacity: 0.9; }

    /* Stepper Styles */
    .stepper { display: flex; justify-content: space-between; position: relative; margin: 0 -10px; }
    .stepper:before { content: ''; position: absolute; top: 50%; left: 20px; right: 20px; height: 2px; background-color: rgba(255,255,255,0.3); transform: translateY(-50%); }
    .step { display: flex; flex-direction: column; align-items: center; text-align: center; position: relative; z-index: 1; width: 80px; }
    .step-icon { width: 40px; height: 40px; border-radius: 50%; background-color: rgba(255,255,255,0.3); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-bottom: 8px; border: 2px solid transparent; transition: all 0.3s ease; }
    .step-label { font-size: 12px; font-weight: 500; }
    .step.completed .step-icon, .step.active .step-icon { background-color: white; color: var(--accent-color); }
    .step.completed .step-icon { border-color: #28a745; }
    .step.active .step-icon { border-color: white; transform: scale(1.1); }
    .step.active .step-label { font-weight: bold; }

    .rep-card .rep-header { display: flex; align-items: center; gap: 16px; margin-bottom: 16px; }
    .rep-card .rep-avatar { width: 64px; height: 64px; border-radius: 50%; object-fit: cover; }
    .rep-card .rep-info h4 { margin: 0; }
    .rep-card .rep-info p { margin: 0; color: #6a737d; }
    .rep-card .btn { width: 100%; text-align: center; }

    .quick-actions-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; flex-grow: 1; }
    .action-card { border: 1px solid var(--border-color); border-radius: 8px; padding: 16px; text-align: center; display: flex; flex-direction: column; justify-content: center; align-items: center; transition: box-shadow 0.2s; }
    .action-card:hover { box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    .action-card .icon { font-size: 2em; margin-bottom: 8px; }
    .action-card .text { font-weight: 500; }
    
    .activity-log ul { padding: 0; }
    .activity-log li { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--border-color); }
    .activity-log li:last-child { border-bottom: none; }
    .activity-log .date { color: #6a737d; font-size: 14px; white-space: nowrap; margin-left:16px;}
</style>

<div class="client-dashboard">
    <div class="dashboard-grid">

        <!-- Block A: Welcome Banner -->
        <div class="card welcome-banner grid-col-span-2">
            <h2><?php echo t('good_afternoon'); ?>, <?= htmlspecialchars($username) ?>!</h2>
            <?php if ($latest_lead): ?>
                <p><?php echo sprintf(t('your_project_is_in_progress'), htmlspecialchars($latest_lead['company_name'])); ?></p>
                <div class="stepper">
                    <?php foreach ($stages as $index => $label): ?>
                        <?php
                            $class = '';
                            if ($index < $current_stage_index) $class = 'completed';
                            if ($index == $current_stage_index) $class = 'active';
                        ?>
                        <div class="step <?= $class ?>">
                            <div class="step-icon"><?= ($index < $current_stage_index) ? '✓' : $index + 1 ?></div>
                            <span class="step-label"><?= $label ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p><?php echo t('welcome_to_portal'); ?></p>
            <?php endif; ?>
        </div>

        <!-- Block B: Your Representative -->
        <div class="card rep-card">
            <h3 class="card-title"><?php echo t('your_dedicated_consultant'); ?></h3>
            <?php if ($representative): ?>
                <div class="rep-header">
                    <img src="<?= htmlspecialchars($representative['avatar_url'] ?? 'https://via.placeholder.com/64') ?>" alt="Rep Avatar" class="rep-avatar">
                    <div class="rep-info">
                        <h4><?= htmlspecialchars($representative['full_name']) ?></h4>
                        <p><?php echo t('customer_success_manager'); ?></p>
                    </div>
                </div>
                <p><?php echo t('phone'); ?>: <?= htmlspecialchars($representative['phone'] ?? t('not_provided')) ?></p>
                <a href="mailto:<?= htmlspecialchars($representative['email']) ?>" class="btn btn--primary"><?php echo t('send_email'); ?></a>
            <?php else: ?>
                <p><?php echo t('matching_consultant'); ?></p>
                <p><?php echo t('consultant_contact_info_will_appear'); ?></p>
            <?php endif; ?>
        </div>

        <!-- Block C: Quick Actions -->
        <div class="card">
            <h3 class="card-title"><?php echo t('quick_actions'); ?></h3>
            <div class="quick-actions-grid">
                <a href="index.php?page=lead_create_form" class="action-card">
                    <div class="icon">+</div>
                    <div class="text"><?php echo t('create_new_project'); ?></div>
                </a>
                <a href="index.php?page=profile_edit" class="action-card">
                    <div class="icon">👤</div>
                    <div class="text"><?php echo t('complete_personal_information'); ?></div>
                </a>
            </div>
        </div>

        <!-- Block D: Recent Activity -->
        <div class="card grid-col-span-2 activity-log">
             <h3 class="card-title"><?php echo t('recent_activity'); ?></h3>
             <?php if (empty($all_leads)): ?>
                <p><?php echo t('no_recent_activity'); ?></p>
             <?php else: ?>
                <ul>
                    <?php foreach ($all_leads as $lead): ?>
                    <li>
                        <span><?php echo sprintf(t('you_submitted_application_on'), date('Y-m-d', strtotime($lead['created_at'])), htmlspecialchars($lead['company_name'])); ?></span>
                        <span class="date"><?php echo t('current_status'); ?>: <?= htmlspecialchars($lead['status']) ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
             <?php endif; ?>
        </div>
    </div>
</div>
