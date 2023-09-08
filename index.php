<html>

<body>
    <h1 class="big_blue">יש להזין מספר משתתף</h1>

    <br>

    <div class = "flex_container">
        <div class="content">        
            <form action="pid_submit.php" method="post">
            
                <label for="participant_num"><a class="question">מספר משתתף</a></label>
                
                <br>
                
                <input type="number" name="participant_num" min="1" max="99999" step="1" required>
                
                <br><br><br>
                
                <button class='next' type = 'submit' name = 'submit'>
                <a>המשך </a>
                </button>
                
            </form>
        </div>
    </div>    

</body>

</html>
