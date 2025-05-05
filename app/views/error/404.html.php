<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page non trouvée</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f9f9f9;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
        }
        
        .container {
            text-align: center;
            padding: 2rem;
            position: relative;
            z-index: 10;
        }
        
        h1 {
            font-size: 120px;
            font-weight: 900;
            color: #ff7f2a;
            text-shadow: 2px 2px 0 #7fdb74;
            letter-spacing: -5px;
            margin-bottom: 20px;
            position: relative;
            animation: float 6s ease-in-out infinite;
        }
        
        h1::before, h1::after {
            content: "404";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.4;
        }
        
        h1::before {
            color: #ff7f2a;
            animation: glitch-1 2.5s infinite linear alternate-reverse;
        }
        
        h1::after {
            color: #7fdb74;
            animation: glitch-2 2s infinite linear alternate-reverse;
        }
        
        h2 {
            font-size: 32px;
            color: #333;
            margin-bottom: 20px;
            animation: fadeIn 1s ease-in-out;
        }
        
        p {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
            max-width: 600px;
            animation: fadeIn 1.5s ease-in-out;
        }
        
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #ff7f2a, #ff9f4a);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 15px rgba(255, 127, 42, 0.3);
            position: relative;
            overflow: hidden;
            animation: fadeIn 2s ease-in-out;
        }
        
        .btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(255, 127, 42, 0.4);
        }
        
        .btn::after {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(to right, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0));
            transform: rotate(45deg);
            transition: 0.5s;
            opacity: 0;
        }
        
        .btn:hover::after {
            animation: sheen 1s forwards;
        }
        
        /* Animations */
        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-20px);
            }
        }
        
        @keyframes glitch-1 {
            0%, 100% {
                clip-path: inset(80% 0 0 0);
                transform: translate(-3px, 5px);
            }
            20% {
                clip-path: inset(20% 0 80% 0);
                transform: translate(3px, -5px);
            }
            40% {
                clip-path: inset(50% 0 30% 0);
                transform: translate(3px, 2px);
            }
            60% {
                clip-path: inset(30% 0 70% 0);
                transform: translate(-5px, 5px);
            }
            80% {
                clip-path: inset(70% 0 10% 0);
                transform: translate(5px, -2px);
            }
        }
        
        @keyframes glitch-2 {
            0%, 100% {
                clip-path: inset(20% 0 80% 0);
                transform: translate(5px, -2px);
            }
            20% {
                clip-path: inset(70% 0 10% 0);
                transform: translate(-5px, 5px);
            }
            40% {
                clip-path: inset(30% 0 70% 0);
                transform: translate(3px, 2px);
            }
            60% {
                clip-path: inset(50% 0 30% 0);
                transform: translate(3px, -5px);
            }
            80% {
                clip-path: inset(80% 0 0 0);
                transform: translate(-3px, 5px);
            }
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes sheen {
            0% {
                opacity: 0;
                transform: rotate(45deg) translate(-100%, -100%);
            }
            100% {
                opacity: 1;
                transform: rotate(45deg) translate(100%, 100%);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <h2>Page non trouvée</h2>
        <p>La page que vous recherchez n'existe pas.</p>
        <a href="?page=login" class="btn">Retour à la page de connexion</a>
    </div>
</body>
</html>