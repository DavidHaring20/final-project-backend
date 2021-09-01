<html>
<body>
        <div class="top-section">
            <div class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="gray">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 
                        2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" 
                    />
                  </svg>
            </div>

            <div class="header">
                PocketMenu Registration E-mail
            </div>
        </div>

        <div class="middle-section">
            <p class="mid-paragraph">
                PASSCODE
            </p>

            <span class="span-passcode">
                {{ $passcode }}
            </span>
        </div>

        <div class="bottom-section">
            <p class="bottom-text"> If you haven't tried to Log in to pocketmenu.com please 
                ignore this e-mail.
            </p>
        </div>
    </body>
</html>

<style>
    * {
        margin: 0;
        padding: 0;
        font-size: 1.5rem;
        color: rgb(75, 85, 99);
    }

    .top-section {
        display: flex;
        flex-direction: row;
        width: 100%;
        margin: 0.5rem 0 0 0.5rem; 
    }

    .header {
        width: 100%;
        align-self: center;
        text-align: center;
    }

    .icon {
        width: 2rem;
        border-right: 2px solid rgb(75, 85, 99);
        padding-right: 2.5rem;
    }

    .middle-section {
        margin-top: 5rem;
        margin-bottom: 8rem;
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-items: center;
        align-items: center;
    }

    .mid-paragraph {
        text-align: center;
        padding-bottom: 0.5rem;
        width: 15rem;
        color: black;
        border-bottom: 2px solid black;
    }

    .span-passcode {
        margin-top: 1rem;
    }

    .bottom-section {
        width: 100%;
        display: flex;
        justify-content: center;
    }

    .bottom-text {
        width: 20ch;
        border-top: 2px solid black;
        font-size: 1rem;
        text-align: center;
    }
</style>