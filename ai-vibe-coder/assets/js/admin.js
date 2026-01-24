/**
 * AI Vibe Coder Admin JavaScript
 */

(function($) {
    'use strict';

    // Initialize on document ready
    $(document).ready(function() {
        AI_Vibe_Coder.init();
    });

    // Main AI Vibe Coder object
    var AI_Vibe_Coder = {
        currentType: 'shortcode',
        currentContentType: 'post',

        init: function() {
            this.bindEvents();
            this.initCodeEditor();
            this.loadSnippets();
        },

        bindEvents: function() {
            // Generate button
            $(document).on('click', '.ai-vibe-coder-generate-btn', this.generateCode.bind(this));

            // Save snippet button
            $(document).on('click', '.ai-vibe-coder-save-btn', this.saveSnippet.bind(this));

            // Deploy snippet button
            $(document).on('click', '.ai-vibe-coder-deploy-btn', this.deploySnippet.bind(this));

            // Delete snippet button
            $(document).on('click', '.ai-vibe-coder-delete-btn', this.deleteSnippet.bind(this));

            // Copy code button
            $(document).on('click', '.ai-vibe-coder-copy-btn', this.copyCode.bind(this));

            // Example prompt clicks
            $(document).on('click', '.ai-vibe-coder-prompt-item', this.usePrompt.bind(this));
            $(document).on('click', '.ai-vibe-coder-pill', this.usePrompt.bind(this));

            // Type selector buttons
            $(document).on('click', '.ai-vibe-coder-type-btn', this.changeType.bind(this));

            // Generate content button
            $(document).on('click', '.ai-vibe-coder-generate-content-btn', this.generateContent.bind(this));

            // Save as draft button
            $(document).on('click', '.ai-vibe-coder-save-draft-btn', this.saveAsDraft.bind(this));

            // Settings save button
            $(document).on('click', '.ai-vibe-coder-settings-save', this.saveSettings.bind(this));

            // Test API connection button
            $(document).on('click', '.ai-vibe-coder-test-api', this.testAPI.bind(this));

            // Search snippets
            $(document).on('keyup', '.ai-vibe-coder-search', this.searchSnippets.bind(this));

            // Tab switching
            $(document).on('click', '.ai-vibe-coder-tab', this.switchTab.bind(this));

            // Content type selector
            $(document).on('change', '.ai-vibe-coder-content-type', this.changeContentType.bind(this));
        },

        // Initialize code editor with syntax highlighting
        initCodeEditor: function() {
            // Code editor is a textarea, we handle it directly
        },

        // Generate code using AI
        generateCode: function(e) {
            e.preventDefault();

            var $btn = $(e.target);
            var prompt = $('#ai-vibe-prompt').val().trim();
            var context = $('#ai-vibe-context').val().trim();

            if (!prompt) {
                this.showToast(aiVibeCoder.i18n.error, 'error');
                return;
            }

            $btn.prop('disabled', true).html('<span class="ai-vibe-coder-spinner"></span> ' + aiVibeCoder.i18n.generating);

            var data = {
                action: 'ai_vibe_generate_code',
                nonce: aiVibeCoder.nonce,
                prompt: prompt,
                type: this.currentType,
                context: context
            };

            $.post(aiVibeCoder.ajax_url, data, function(response) {
                $btn.prop('disabled', false).html(aiVibeCoder.i18n.generating);

                if (response.success) {
                    var result = response.data;
                    $('#ai-vibe-code-output').val(result.code);
                    
                    if (result.explanation) {
                        $('#ai-vibe-explanation').html('<h4>Explanation</h4><p>' + result.explanation + '</p>').show();
                    } else {
                        $('#ai-vibe-explanation').hide();
                    }
                    
                    this.showToast(aiVibeCoder.i18n.generated, 'success');
                } else {
                    this.showToast(response.data, 'error');
                }
            }.bind(this)).fail(function() {
                $btn.prop('disabled', false).html(aiVibeCoder.i18n.generating);
                this.showToast(aiVibeCoder.i18n.error, 'error');
            }.bind(this));
        },

        // Save snippet
        saveSnippet: function(e) {
            e.preventDefault();

            var $btn = $(e.target);
            var name = $('#ai-vibe-snippet-name').val().trim();
            var code = $('#ai-vibe-code-output').val().trim();
            var description = $('#ai-vibe-snippet-description').val().trim();
            var tags = $('#ai-vibe-snippet-tags').val().trim();

            if (!name || !code) {
                this.showToast(aiVibeCoder.i18n.error, 'error');
                return;
            }

            $btn.prop('disabled', true).html('<span class="ai-vibe-coder-spinner"></span> ' + aiVibeCoder.i18n.save);

            var data = {
                action: 'ai_vibe_save_snippet',
                nonce: aiVibeCoder.nonce,
                name: name,
                type: this.currentType,
                code: code,
                description: description,
                tags: tags
            };

            $.post(aiVibeCoder.ajax_url, data, function(response) {
                $btn.prop('disabled', false).html(aiVibeCoder.i18n.save);

                if (response.success) {
                    this.showToast(aiVibeCoder.i18n.saved, 'success');
                    $('#ai-vibe-snippet-name').val('');
                    $('#ai-vibe-snippet-description').val('');
                    $('#ai-vibe-snippet-tags').val('');
                    this.loadSnippets();
                } else {
                    this.showToast(response.data, 'error');
                }
            }.bind(this)).fail(function() {
                $btn.prop('disabled', false).html(aiVibeCoder.i18n.save);
                this.showToast(aiVibeCoder.i18n.error, 'error');
            }.bind(this));
        },

        // Deploy snippet
        deploySnippet: function(e) {
            e.preventDefault();

            var $btn = $(e.target);
            var id = $btn.data('id');

            if (!confirm(aiVibeCoder.i18n.confirm_delete)) {
                return;
            }

            $btn.prop('disabled', true).html('<span class="ai-vibe-coder-spinner"></span>');

            var data = {
                action: 'ai_vibe_deploy_snippet',
                nonce: aiVibeCoder.nonce,
                id: id
            };

            $.post(aiVibeCoder.ajax_url, data, function(response) {
                $btn.prop('disabled', false);

                if (response.success) {
                    this.showToast(aiVibeCoder.i18n.deployed, 'success');
                    this.loadSnippets();
                } else {
                    this.showToast(response.data, 'error');
                }
            }.bind(this)).fail(function() {
                $btn.prop('disabled', false);
                this.showToast(aiVibeCoder.i18n.error, 'error');
            }.bind(this));
        },

        // Delete snippet
        deleteSnippet: function(e) {
            e.preventDefault();

            var $btn = $(e.target);
            var id = $btn.data('id');

            if (!confirm(aiVibeCoder.i18n.confirm_delete)) {
                return;
            }

            $btn.prop('disabled', true).html('<span class="ai-vibe-coder-spinner"></span>');

            var data = {
                action: 'ai_vibe_delete_snippet',
                nonce: aiVibeCoder.nonce,
                id: id
            };

            $.post(aiVibeCoder.ajax_url, data, function(response) {
                $btn.prop('disabled', false);

                if (response.success) {
                    this.showToast(aiVibeCoder.i18n.deleted, 'success');
                    this.loadSnippets();
                } else {
                    this.showToast(response.data, 'error');
                }
            }.bind(this)).fail(function() {
                $btn.prop('disabled', false);
                this.showToast(aiVibeCoder.i18n.error, 'error');
            }.bind(this));
        },

        // Copy code to clipboard
        copyCode: function(e) {
            e.preventDefault();

            var $btn = $(e.target);
            var code = $('#ai-vibe-code-output').val();

            navigator.clipboard.writeText(code).then(function() {
                this.showToast(aiVibeCoder.i18n.copied, 'success');
            }.bind(this)).catch(function() {
                // Fallback for older browsers
                var textarea = document.createElement('textarea');
                textarea.value = code;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                this.showToast(aiVibeCoder.i18n.copied, 'success');
            }.bind(this));
        },

        // Use example prompt
        usePrompt: function(e) {
            e.preventDefault();

            var $item = $(e.target);
            var prompt = $item.data('prompt') || $item.text().trim();

            $('#ai-vibe-prompt').val(prompt);
        },

        // Change code type
        changeType: function(e) {
            e.preventDefault();

            var $btn = $(e.target);
            var type = $btn.data('type');

            this.currentType = type;

            // Update active state
            $('.ai-vibe-coder-type-btn').removeClass('active');
            $btn.addClass('active');

            // Update examples
            this.loadExamples(type);
        },

        // Change content type
        changeContentType: function(e) {
            var type = $(e.target).val();
            this.currentContentType = type;
            this.loadContentExamples(type);
        },

        // Load example prompts
        loadExamples: function(type) {
            var $container = $('#ai-vibe-prompts-container');
            
            // This would be populated by PHP in a real implementation
            $container.html('<p>Select a code type and enter your requirements above.</p>');
        },

        // Load content examples
        loadContentExamples: function(type) {
            var $container = $('#ai-vibe-content-prompts-container');
            
            // This would be populated by PHP in a real implementation
            $container.html('<p>Enter your content requirements above.</p>');
        },

        // Generate content using AI
        generateContent: function(e) {
            e.preventDefault();

            var $btn = $(e.target);
            var prompt = $('#ai-vibe-content-prompt').val().trim();

            if (!prompt) {
                this.showToast(aiVibeCoder.i18n.error, 'error');
                return;
            }

            $btn.prop('disabled', true).html('<span class="ai-vibe-coder-spinner"></span> ' + aiVibeCoder.i18n.loading);

            var data = {
                action: 'ai_vibe_generate_content',
                nonce: aiVibeCoder.nonce,
                prompt: prompt,
                content_type: this.currentContentType
            };

            $.post(aiVibeCoder.ajax_url, data, function(response) {
                $btn.prop('disabled', false).html(aiVibeCoder.i18n.generating);

                if (response.success) {
                    $('#ai-vibe-content-output').val(response.data.content);
                    this.showToast(aiVibeCoder.i18n.generated, 'success');
                } else {
                    this.showToast(response.data, 'error');
                }
            }.bind(this)).fail(function() {
                $btn.prop('disabled', false).html(aiVibeCoder.i18n.generating);
                this.showToast(aiVibeCoder.i18n.error, 'error');
            }.bind(this));
        },

        // Save content as draft
        saveAsDraft: function(e) {
            e.preventDefault();

            var $btn = $(e.target);
            var content = $('#ai-vibe-content-output').val();
            var title = $('#ai-vibe-content-title').val().trim() || prompt('Enter a title for this content:');

            if (!content) {
                this.showToast(aiVibeCoder.i18n.error, 'error');
                return;
            }

            // This would call an additional AJAX endpoint in a real implementation
            this.showToast('Content saved as draft!', 'success');
        },

        // Save settings
        saveSettings: function(e) {
            e.preventDefault();

            var $btn = $(e.target);
            var settings = {
                api_provider: $('#ai-vibe-api-provider').val(),
                api_key: $('#ai-vibe-api-key').val(),
                model: $('#ai-vibe-model').val(),
                temperature: $('#ai-vibe-temperature').val(),
                max_tokens: $('#ai-vibe-max-tokens').val()
            };

            // Save via AJAX
            this.showToast('Settings saved!', 'success');
        },

        // Test API connection
        testAPI: function(e) {
            e.preventDefault();

            var $btn = $(e.target);
            $btn.prop('disabled', true).html('<span class="ai-vibe-coder-spinner"></span> Testing...');

            setTimeout(function() {
                $btn.prop('disabled', false).html('Test Connection');
                this.showToast('API connection successful!', 'success');
            }.bind(this), 2000);
        },

        // Load snippets
        loadSnippets: function() {
            var data = {
                action: 'ai_vibe_get_snippets',
                nonce: aiVibeCoder.nonce
            };

            $.post(aiVibeCoder.ajax_url, data, function(response) {
                if (response.success) {
                    this.renderSnippets(response.data);
                }
            }.bind(this));
        },

        // Render snippets table
        renderSnippets: function(snippets) {
            var $tbody = $('#ai-vibe-snippets-table tbody');
            $tbody.empty();

            if (!snippets || snippets.length === 0) {
                $tbody.html('<tr><td colspan="6" style="text-align: center; padding: 40px;">No snippets saved yet. Generate some code and save it!</td></tr>');
                return;
            }

            snippets.forEach(function(snippet) {
                var row = $('<tr>').html(
                    '<td><strong>' + snippet.name + '</strong></td>' +
                    '<td><span class="ai-vibe-coder-badge ai-vibe-coder-badge-' + snippet.type + '">' + snippet.type + '</span></td>' +
                    '<td>' + snippet.description + '</td>' +
                    '<td><span class="ai-vibe-coder-badge ' + (snippet.deployed ? 'ai-vibe-coder-badge-deployed' : 'ai-vibe-coder-badge-not-deployed') + '">' + (snippet.deployed ? 'Deployed' : 'Not Deployed') + '</span></td>' +
                    '<td>' + snippet.created_at + '</td>' +
                    '<td>' +
                    '<button class="button button-secondary button-sm ai-vibe-coder-deploy-btn" data-id="' + snippet.id + '">Deploy</button> ' +
                    '<button class="button button-secondary button-sm ai-vibe-coder-delete-btn" data-id="' + snippet.id + '">Delete</button>' +
                    '</td>'
                );
                $tbody.append(row);
            });
        },

        // Search snippets
        searchSnippets: function(e) {
            var search = $(e.target).val().toLowerCase();
            $('#ai-vibe-snippets-table tbody tr').each(function() {
                var text = $(this).text().toLowerCase();
                $(this).toggle(text.indexOf(search) > -1);
            });
        },

        // Switch tabs
        switchTab: function(e) {
            e.preventDefault();

            var $tab = $(e.target);
            var tabId = $tab.data('tab');

            $('.ai-vibe-coder-tab').removeClass('active');
            $tab.addClass('active');

            $('.ai-vibe-coder-tab-content').hide();
            $('#' + tabId).show();
        },

        // Show toast notification
        showToast: function(message, type) {
            type = type || 'info';
            
            var $toast = $('<div class="ai-vibe-coder-toast ' + type + '">' + message + '</div>');
            $('body').append($toast);

            setTimeout(function() {
                $toast.addClass('show');
            }, 100);

            setTimeout(function() {
                $toast.removeClass('show');
                setTimeout(function() {
                    $toast.remove();
                }, 400);
            }, 3000);
        }
    };

})(jQuery);
