
REM Set the path to your php.exe here
set phpPath="D:\Programme\xampp\php\php.exe"

REM Start ManiaControl
START "" /B %phpPath% -f "ManiaControl.php" 2>&1
