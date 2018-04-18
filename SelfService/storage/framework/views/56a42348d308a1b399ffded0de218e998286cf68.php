<!-- Javascript  -->

<script>

// loopt elke seconde
window.setInterval(function(){
  getInfo();
  myFunction();
}, 500);

// variables input = RFID code
var input = "042ab9a2";
var filter, li, a, i, pr, kg, test,newKg,newRfid;

function getInfo() {
    {
    $.ajax({
    type: 'GET',
    url: '/getInfo',
    data: '_token = <?php echo csrf_token() ?>',
    dataType: 'json',

    success: function (response) {
      // We get the element having id of display_info and put the response inside it
        test = response;
        test = JSON.stringify(test);
        test = JSON.parse(test);
        newRfid = test.rfid;
        newKg = Number(test.kg);
        input = newRfid;
        console.log(newKg);


    }
    });


  }
}
function myFunction() {
    // Declare variables
    ul = document.getElementById("myUl");
    li = ul.getElementsByTagName('li');

    // maak een loop die door alle rfid codes gaat en de variables aanpast
    for (i = 0; i < li.length; i++) {
        a = li[i].getElementsByTagName("a")[0];
        li[i].getElementsByClassName('price2')[0].innerHTML = newKg;
        pr = li[i].getElementsByClassName('pr')[0].innerHTML;
        kg = li[i].getElementsByClassName('price2')[0].innerHTML;
        // rekent de eindprijs uit
        num3 = pr * newKg;
        // zorgt ervoor dat het een prijs nummer wordt
        num3 = parseFloat(Math.round(num3 * 100) / 100).toFixed(2);
        // zet de prijs in de html en de hidden html
        li[i].getElementsByClassName('price')[0].innerHTML = "$ " + num3;
        li[i].getElementsByClassName('endprice')[0].value = num3;
        //li[i].getElementsByClassName("endprice")[0].value = num3.toString();
        li[i].getElementsByClassName('kg')[0].value = kg.toString();
        // zorgt ervoor dat alleen het product met de overeen komende RFID wordt getoont
        if (a.innerHTML.indexOf(input) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}

</script>
<script src="<?php echo e(URL::asset('js/jquery-3.3.1.js')); ?>"></script>
<link href="<?php echo e(URL::asset('css/style.css')); ?>" rel="stylesheet">

<div class="achtergrond">
  <ul id="myUl">
    <?php $__currentLoopData = $product; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <li>
      <form action="/addProduct" method="POST" role="create">
      <?php echo e(csrf_field()); ?>

        <div class="product<?php echo e($product->id); ?>">
          <div class="container-fluid vertical-center">
            <div class="row">
              <div class="col-xs-10 col-xs-offset-1">
                <div class="row">
                  <div class="col-xs-12">
                    <div class="product name"><?php echo e($product->name); ?></div>
                  </div>
                </div>
                <div class="row row_padding">
                  <div class="col-xs-5 col-sm-8"><div class="price">price</div></div>
                  <div class="col-xs-5"><div class="price2">0.600</div></div>
                </div>

                <!-- hidden input voor de query  -->
                <a hidden><?php echo e($product->rfid); ?></a>
                <p class="pr" hidden><?php echo e($product->price); ?></p>
                <input class="kg" name="kg" hidden value="">
                <input hidden value="<?php echo e($product->rfid); ?>" name="rfid">
                <input hidden value="<?php echo e($product->name); ?>" name="name">
                <input class="endprice" value="" name="endprice" hidden>

                <div class="row row_padding">
                  <div class="col-xs-7">
                    <div class="info">
                      <?php echo e($product->info); ?>

                    </div>
                  </div>
                  <div class="col-xs-5">
                    <button class="button" type="submit">Reken af</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </li>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </ul>
</div>
