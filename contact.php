<!DOCTYPE html>  
<html>  
<meta charset="utf-8">
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<head>
<title>Ceci est un titre</title>
<link href="contact.css" rel="stylesheet"> 
</head>
<body>
<?php 
include 'header.php';
?>
<div class="container contact-form">
            <div class="contact-image">
                <img src="https://image.ibb.co/kUagtU/rocket_contact.png" alt="rocket_contact"/>
            </div>
            <form method="post">
                <h3>Drop Us a Message</h3>
               <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" name="txtName" class="form-control" placeholder="Votre nom *" value="" />
                        </div>
                        <br>
                        <div class="form-group">
                            <input type="text" name="txtfirstname" class="form-control" placeholder="Votre prénom *" value="" />
                        </div>
                        <br>
                        <div class="form-group">
                            <input type="text" name="txtEmail" class="form-control" placeholder="Votre email *" value="" />
                        </div>
                        <br>
                        <div class="form-group">
                            <input type="text" name="txtPhone" class="form-control" placeholder="Votre n° de téléphone *" value="" />
                        </div>
                        <br>
                    <div class="col-md-6">
                        <div class="form-group">
                            <textarea name="txtMsg" class="form-control" placeholder="Votre message *" style="width: 100%; height: 150px;"></textarea>
                        </div>
                        <br>
                        <div class="form-group">
                            <input type="submit" name="btnSubmit" class="btnContact" value="Envoyer" />
                        </div>
                    </div>
                    </div>
                </div>
            </form>
</div>

</body>
</html>