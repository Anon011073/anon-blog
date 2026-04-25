<?php
return [
    'name' => 'Contact Form',
    'description' => 'Adds a simple contact form using the [contact] shortcode.',
    'version' => '1.0',
    'author' => 'Jules',
    'hooks' => [
        'render_content' => function($content) {
            if (strpos($content, '[contact]') !== false) {
                $form = '
                <div class="contact-form-plugin">
                    <form method="POST" action="">
                        <div style="margin-bottom: 15px;">
                            <label style="display:block;margin-bottom:5px;">Name</label>
                            <input type="text" name="contact_name" required style="width:100%;padding:10px;border:1px solid #ccc;border-radius:4px;">
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="display:block;margin-bottom:5px;">Email</label>
                            <input type="email" name="contact_email" required style="width:100%;padding:10px;border:1px solid #ccc;border-radius:4px;">
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="display:block;margin-bottom:5px;">Message</label>
                            <textarea name="contact_message" required style="width:100%;padding:10px;border:1px solid #ccc;border-radius:4px;height:150px;"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                </div>';

                if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_message'])) {
                    $form = '<div style="background:#dff0d8;color:#3c763d;padding:15px;border-radius:4px;margin-bottom:20px;">Thank you! Your message has been sent. (Simulation)</div>' . $form;
                }

                return str_replace('[contact]', $form, $content);
            }
            return $content;
        }
    ]
];
