<!DOCTYPE html>
<html lang="en">
<body>
<div class="LoginBox">
    <section id="LoginContent">
        <form id='login' method='post' accept-charset='UTF-8'>
            <h2 class="LoginInfo">
                To start RGDX or to get in the queue, enter your first name below:
            </h2>
            <div class="LogInContainer">
                <input type='hidden' name='submitted' id='submitted' value='1'/>
                <div class="LogInField">
                    <input autofocus type="text" maxlength="24" placeholder="First Name" required name="firstname"/>
                </div>
                <div class="LogInBtn">
                     <button type="submit" class="fa fa-chevron-right"></button>
                </div>
            </div>
        </form>
    </section>
</div>
</body>
<script type="text/javascript">
$(function() {
// Handler for .ready() called.
$('#login').submit(function( event ) {
    $.ajax({
            url: 'queuepage.php',
            type: 'POST',
            dataType: 'html',
            data: $('#login').serialize(),
            success: function(content)
            {
                $("#controls").html(content);
            }
    });
    event.preventDefault();
});

});
</script>
</html>