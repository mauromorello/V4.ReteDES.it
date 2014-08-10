<?php
  if(!IsLoggedIn()){
    header("location: http://www.retedes.it");
  }else{
    $stmt = $db->prepare("UPDATE maaking_users SET
                            maaking_users.last_activity = NOW(),
                            maaking_users.user_start_page = :user_agent
                            WHERE maaking_users.userid ='"._USER_ID."';");
    $stmt->bindParam(':user_agent', $_SERVER['HTTP_USER_AGENT'], PDO::PARAM_STR);
    $stmt->execute();
  }
?>
