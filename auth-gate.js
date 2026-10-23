fetch("auth_api.php?action=status", {credentials:"same-origin"})
  .then(r=>r.json()).then(data=>{if(!data.logged_in) location.replace("login.php");})
  .catch(()=>location.replace("login.php"));
