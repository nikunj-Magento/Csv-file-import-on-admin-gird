require(
    [
        'jquery',
        'mage/translate',
    ],
    function ($) {
     $('#barcodegrid_barcode').attr('minlength', '2');
     $('#barcodegrid_barcode').attr('maxlength', '13');
    }
);