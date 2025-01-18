    </div><!--//app-content-->
</div><!--//app-wrapper-->   

<!-- jQuery            -->
<script type="text/javascript" src="assets/js/jquery.js"></script>
<!-- FontAwesome JS-->
<script defer src="assets/plugins/fontawesome/js/all.min.js"></script>

<!-- Bootstrap JS and Popper.js (necessary for modals to work) -->
<script src="assets/plugins/popper.min.js"></script>
<script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>

<!-- Charts JS -->
<script src="assets/plugins/chart.js/chart.min.js"></script> 
<script src="assets/js/index-charts.js"></script> 

<!-- <link rel="stylesheet" href="css/dashboard.css" /> -->
<script src="assets/js/dataTables.min.js"></script>

<?php if (isset($jsFile)): ?>
    <script src="js/<?php echo $jsFile; ?>"></script>
<?php endif; ?>
</body>