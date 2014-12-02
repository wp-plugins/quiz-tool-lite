<link rel="stylesheet" type="text/css" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/smoothness/jquery-ui.css">
<script>
var defaultTab = (parseInt(getParam('tab'))-1);
jQuery("document").ready(function()
{
    jQuery( "#tabs" ).tabs({active: defaultTab});
});
</script>