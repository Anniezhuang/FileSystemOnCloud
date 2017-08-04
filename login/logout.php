<?php
unset ($_SESSION['username']);
unset ($_SESSION['user_id']);
unset($_SESSION);
session_unset();
session_destroy();
echo "是否删除cookie\n\n";
?>
<html>
<body>
<tr>

   <td>
     <form method = "post" action = "../login/c.php">
      <input type = "radio" name = "delete" value = "delete">
      <span  style=" font-family:verdana;">是 </span>
      <input type = "radio" name = "logout" value = "logout" ><span  style=" font-family:verdana;">否</span>
      <br>
      <input type = "submit" name = "submit" value = "Submit">
     </form>
   </td>
</tr>

</body>
</html>
