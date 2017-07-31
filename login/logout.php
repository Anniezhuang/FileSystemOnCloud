
<html>
<body>
  <?php
  echo "是否删除cookie\n\n";
  ?>
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
<?php   if(isset($_POSt['delete']))
{

  // setcookie('user_id',' ',time()-3600);
  // setcookie('username',' ',time()-3600);
  session_destroy ();
}?>
</body>
</html>
