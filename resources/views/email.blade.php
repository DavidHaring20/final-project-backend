<html>
    <head>
        <style>
            * {
                margin: 0;
                padding: 0;
                font-size: 1.5rem;
                color: rgb(75, 85, 99);
            }

            .top-section {
                width: 100%;
                margin: 0.5rem 0 0 0.5rem; 
            }

            .header {
                width: 100%;
                text-align: center;
            }

            .middle-section {
                width: 100%;
                display: flex;
                justify-content: center;
            }

            .margin-top {
                margin-top: 5rem;
            }

            .margin-bottom {
                margin-bottom: 8rem;
            }

            .mid-paragraph {
                width: 100%;
                padding-bottom: 0.5rem;
                color: black;
                text-align: center;
            }
            
            .bottom-border {
                width: 40%;
                border-bottom: 2px solid black;
                margin-left: 30%;
            }

            .span-passcode {
                margin-top: 1rem;
                text-align: center;
                width: 100%;
            }

            .bottom-section {
                width: 100%;
            }

            .bottom-section-top-border {
                border-top: 2px solid black;
                width: 80%;
                margin-left: 10%;
            }

            .bottom-text {
                margin-top: 0.4rem;
                font-size: 1rem;
                text-align: center;
            }

            a {
                font-size: 1.1rem;
                text-decoration: none;
            }
        </style>
    </head>

    <body>
        <div class="top-section">

            <div class="header">
                PocketMenu Registration E-mail
            </div>
        </div>

        <div class="middle-section margin-top">
            <p class="mid-paragraph">
                PASSCODE
            </p>    
        </div>

        <div class="middle-section">
            <div class="bottom-border"></div>
        </div>

        <div class="middle-section margin-bottom">
            <span class="span-passcode">
                {{ $passcode }}
            </span>
        </div>

        <div class="bottom-section">
            <div class="bottom-section-top-border"></div>

            <p class="bottom-text"> If you haven't tried to Log in to <a href="https://test.pocketmenu.club/">PocketMenu page</a> please 
                ignore this e-mail.
            </p>
        </div>
    </body>
</html>