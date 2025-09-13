<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Super UI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Add Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #818cf8;
            --text-color: #ffffff;
            --background-overlay: rgba(17, 24, 39, 0.7);
            --form-background: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: linear-gradient(var(--background-overlay), var(--background-overlay)),
                        url('https://images.unsplash.com/photo-1497215728101-856f4ea42174?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80') no-repeat center center/cover;
            color: var(--text-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 1rem;
        }

        #container {
            background: var(--form-background);
            backdrop-filter: blur(16px);
            padding: 2.5rem;
            border-radius: 1rem;
            width: 100%;
            max-width: 32rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1),
                        0 10px 10px -5px rgba(0, 0, 0, 0.04);
            animation: slideUp 0.6s ease-out;
        }

        header {
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1rem;
            letter-spacing: -0.025em;
        }

        .description {
            text-align: center;
            font-size: 1.125rem;
            margin-bottom: 2rem;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: rgba(255, 255, 255, 0.9);
        }

        /* Style for input containers with icons */
        .input-container {
            position: relative;
        }

        input,
        textarea {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem; /* Adjusted padding for icons */
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 0.5rem;
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-color);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input:focus,
        textarea:focus {
            border-color: var(--primary-color);
            background: rgba(255, 255, 255, 0.1);
            outline: none;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.3);
        }

        textarea {
            resize: vertical;
            min-height: 8rem;
            padding-left: 2.5rem; /* Adjusted for icon */
        }

        /* Icon styling */
        .input-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
        }

        .textarea-icon {
            top: 2.25rem; /* Adjusted for textarea */
        }

        .submit-btn {
            width: 100%;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.875rem 1.5rem;
            font-size: 1rem;
            font-weight: 500;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .submit-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-1px);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .back-link {
            position: fixed;
            top: 1.5rem;
            left: 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-color);
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(8px);
        }

        .back-link:hover {
            background: rgba(255, 255, 255, 0.2);
            color: var(--primary-color);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 640px) {
            #container {
                padding: 1.5rem;
            }

            header {
                font-size: 1.75rem;
            }

            .back-link {
                top: 1rem;
                left: 1rem;
            }
        }

        .submit-btn.loading {
            background: var(--secondary-color);
            pointer-events: none;
            position: relative;
        }

        .submit-btn.loading::after {
            content: '';
            position: absolute;
            width: 1.25rem;
            height: 1.25rem;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <a href="#" class="back-link" onclick="window.history.back();">
        <i class="fas fa-arrow-left"></i>
        Back
    </a>
    
    <div id="container">
        <header><i class="fas fa-envelope me-2"></i>Get in Touch</header>
        <p class="description">Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
        
        <form method="post" action="contactDB.php" id="contactForm">
            <div class="form-group">
                <label for="name">Full Name</label>
                <div class="input-container">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" id="name" name="name" placeholder="John Doe" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-container">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" id="email" name="email" placeholder="john@example.com" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="subject">Subject</label>
                <div class="input-container">
                    <i class="fas fa-tag input-icon"></i>
                    <input type="text" id="subject" name="subject" placeholder="How can we help?" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="message">Message</label>
                <div class="input-container">
                    <i class="fas fa-comment textarea-icon input-icon"></i>
                    <textarea id="message" name="message" placeholder="Tell us more about your needs..." required></textarea>
                </div>
            </div>
            
            <button type="submit" class="submit-btn">
                <i class="fas fa-paper-plane"></i>
                <span>Send Message</span>
            </button>
        </form>
    </div>

    <script>
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('.submit-btn');
            submitBtn.classList.add('loading');
            submitBtn.querySelector('span').textContent = 'Sending...';
            submitBtn.querySelector('i').className = 'fas fa-spinner fa-spin';
        });
    </script>
</body>
</html>