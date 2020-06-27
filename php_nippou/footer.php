<footer id="footer">
  Copyright <a href="#">Question</a>. All Rights Reserved.
</footer>

<script src="https://code.jquery.com/jquery-3.0.0.min.js"></script>
<script>
  $(function(){
    var $ftr = $('#footer');
    if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
      $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) +'px;' });
    }