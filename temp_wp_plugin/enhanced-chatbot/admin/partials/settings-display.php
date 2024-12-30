
<div class="wrap juliusai-admin-wrap">
    <h1>JuliusAI ChatBot Settings</h1>
    
    <div class="juliusai-tabs">
        <div class="juliusai-tab active" data-tab="#general-settings">General Settings</div>
        <div class="juliusai-tab" data-tab="#appearance">Appearance</div>
        <div class="juliusai-tab" data-tab="#advanced">Advanced</div>
    </div>

    <form id="juliusai-settings-form">
        <div id="general-settings" class="juliusai-tab-content active">
            <div class="juliusai-form-group">
                <label for="api_key">API Key</label>
                <input type="text" id="api_key" name="api_key" value="<?php echo esc_attr(get_option('juliusai_api_key')); ?>">
            </div>
        </div>

        <div id="appearance" class="juliusai-tab-content">
            <div class="juliusai-form-group">
                <label for="chat_height">Chat Window Height</label>
                <input type="text" id="chat_height" name="chat_height" value="<?php echo esc_attr(get_option('juliusai_chat_height')); ?>">
            </div>
        </div>

        <div id="advanced" class="juliusai-tab-content">
            <div class="juliusai-form-group">
                <label for="custom_css">Custom CSS</label>
                <textarea id="custom_css" name="custom_css"><?php echo esc_textarea(get_option('juliusai_custom_css')); ?></textarea>
            </div>
        </div>

        <button type="submit" class="juliusai-save-btn">Save Settings</button>
    </form>

    <div class="preview-window">
        <div class="preview-toggle">Preview</div>
        <iframe src="about:blank" style="width: 100%; height: 100%; border: none;"></iframe>
    </div>
</div>
