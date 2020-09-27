<button id="rzp-button1" hidden>Pay</button>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
var options = {
    "key": "{{ $response['razorpayId'] }}", // Enter the Key ID generated from the Dashboard
    "amount": "{{ $response['amount'] }}", // Amount is in currency subunits. Default currency is INR. Hence, 50000 refers to 50000 paise
    "currency": "{{ $response['currency'] }}",
    "name": "{{ $response['name'] }}",
    "description": "{{ $response['description'] }}",
    "image": "https://example.com/your_logo",
    "order_id": "{{ $response['orderId'] }}", //This is a sample Order ID. Pass the `id` obtained in the response of Step 1
    "handler": function (response){

        document.getElementById('rzp_paymentid').value = response.razorpay_payment_id;
        document.getElementById('rzp_orderid').value = response.razorpay_order_id;
        document.getElementById('rzp_signature').value = response.razorpay_signature;
        document.getElementById('rzp-paymentresponse').click();
    },
    "prefill": {
        "name": "{{ $response['name'] }}",
        "email": "{{ $response['email'] }}",
        "contact": "{{ $response['contactNumber'] }}"
    },
    "notes": {
        "address": "{{ $response['address'] }}"
    },
    "theme": {
        "color": "#F37254"
    }
};
var rzp1 = new Razorpay(options);
window.onload = function(){

    document.getElementById('rzp-button1').click();
};
document.getElementById('rzp-button1').onclick = function(e){
    rzp1.open();
    e.preventDefault();
}
</script>

<form action="{{ url('complete') }}" method="post" hidden>

<input type="hidden" name="_token" value="{{ csrf_token() }}"> 
<input type="text" class="form-control" id="rzp_paymentid" name="rzp_paymentid"> 
<input type="text" class="form-control" id="rzp_orderid" name="rzp_orderid"> 
<input type="text" class="form-control" id="rzp_signature" name="rzp_signature"> 

<button type="submit" id="rzp-paymentresponse" class="btn btn-primary">Submit</button>




</form>