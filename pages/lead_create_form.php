<?php
// pages/lead_create_form.php
?>
<div class="container public-detail-container">
    <header class="detail-header">
        <h1>发布一个新项目</h1>
        <p>告诉我们您需要什么，我们的专业人士网络将完成剩下的工作。</p>
    </header>

    <div class="main-content-detail">
        <form action="pages/lead_create_action.php" method="POST" class="comment-form" style="border-top: none; padding-top: 0;">
            <div class="form-group">
                <label for="company_name" style="font-size: 1.2rem; font-weight: bold;">为您的项目取一个名字</label>
                <input type="text" id="company_name" name="company_name" class="search-input" style="border-radius: 8px; margin-top: 0.5rem;" placeholder="例如：建立一个响应式网站" required>
            </div>

            <div class="form-group" style="margin-top: 2rem;">
                <label for="description" style="font-size: 1.2rem; font-weight: bold;">描述您的项目</label>
                <textarea id="description" name="description" rows="10" style="margin-top: 0.5rem;" placeholder="详细说明您的需求、期望交付成果、所需技能等。" required></textarea>
            </div>
            
            <div class="form-actions" style="margin-top: 2rem; text-align: right;">
                <a href="?page=client_dashboard" class="btn btn-secondary">取消</a>
                <button type="submit" class="btn btn-primary">提交项目</button>
            </div>
        </form>
    </div>
</div>
