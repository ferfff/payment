Conekta.setPublicKey('key_Jn1Zvf8qsQFuqAJVfWxeMjQ');
      
var conektaSuccessResponseHandler = function(token) {
    var $form = $("#card-form");
    //Inserta el token_id en la forma para que se envíe al servidor
    $form.append($('<input type="hidden" name="conektaTokenId" id="conektaTokenId">').val(token.id));
    $form.get(0).submit(); //Hace submit
};
var conektaErrorResponseHandler = function(response) {
    var $form = $("#card-form");
    $form.find(".card-errors").text(response.message_to_purchaser);
    $form.find("button").prop("disabled", false);
};

//jQuery para que genere el token después de dar click en submit
$(function () {
    /* $( "#bornDate" ).datepicker({
        changeMonth: true,
        changeYear: true,
        minDate: "-100Y",
        maxDate: "-18Y"
    }); */

    $("#card-form").submit(function(event) {
        var $form = $(this);
        // Previene hacer submit más de una vez
        $form.find("button#processPayment").prop("disabled", true);
        Conekta.Token.create($form, conektaSuccessResponseHandler, conektaErrorResponseHandler);
        return false;
    });
});

function onVisaCheckoutReady (){
    //var visaSandbox = window.location.hostname + '/getValues.php';
    var $form = $("#card-form"),
        $currency = $("#currency").val(),
        $quantity = $("#quantity").val();
    V.init({
        apikey: "W401QU4CFGPRRH4VDY6E21boZzLslg3EYNrCfB1meHgNrxLC8",
        sourceId: "angeles_123456",
        paymentRequest:{
            currencyCode: $currency,
            subtotal: $quantity
        },
        settings: {
            displayName: "Rescatando Angeles A.C.",
            locale: "es_MX",
            payment: {  
                cardBrands:["VISA","MASTERCARD"],
                billingCountries:["US","MX"]
            }
        }
    });

    V.on("payment.success", function(payment) {
        console.log(payment);       
        //2158511094311945901
        console.log('callid: ' + payment.callid);
        var callId = payment.callid || null;
        //var visaSandbox = 'https://sandbox.api.visa.com/wallet-services-web/payment/data/'+callId+'?apikey='+apiKey;
        $.post('/getValues.php', {'callid' : callId}, function( dataString ) {
            var data = JSON.parse(dataString);
            //console.log(data);
            console.log(data.body);
            if (data.status !== 200 ) {
                $form.find(".card-errors").text("Hubo un error en el servidor de Visa checkout");
            } else {
                $form.find("#fullName").val(data.body.userData.userFullName);
                $form.find("#addressLine1").val(data.body.paymentInstrument.billingAddress.line1);
                $form.find("#addressLine2").val(data.body.paymentInstrument.billingAddress.line2);
                $form.find("#city").val(data.body.paymentInstrument.billingAddress.city);
                $form.find("#state").val(data.body.paymentInstrument.billingAddress.stateProvinceCode);
                $form.find("#postcode").val(data.body.paymentInstrument.billingAddress.postalCode);
                $form.find("#country").val(data.body.paymentInstrument.billingAddress.countryCode);
                $form.find("#email").val(data.body.userData.userEmail);
                $form.find("#phoneNumber").val(data.body.paymentInstrument.billingAddress.phone);
                $form.find("#nombretarjetahabiente").val(data.body.userData.userFullName);
                $form.find("#cardNumber").val('XXXXXXXXXXXX'+data.body.paymentInstrument.lastFourDigits);
                $form.find("#cardNumber").val('4242424242424242');
                $form.find("#cvc").val('111');
                $form.find("#rfc").val('sdfasfdsfadfdf');
                $form.find("#cardMonth").val(data.body.paymentInstrument.expirationDate.month);
                $form.find("#cardYear").val(data.body.paymentInstrument.expirationDate.year);
            }
        });
    });
    V.on("payment.cancel", function(payment) {
        $form.find(".card-errors").text('Carga visa cancelada');
    });
    V.on("payment.error", function(payment,error) {
        //$form.find(".card-errors").text(JSON.stringify(error));
        console.log(JSON.stringify(error));
        $form.find(".card-errors").text('Hubo un error en la compra');
    });
}