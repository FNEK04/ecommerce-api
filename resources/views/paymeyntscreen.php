<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .payment-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .btn {
            background-color: #007bff;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-success:hover {
            background-color: #1e7e34;
        }
        .message {
            margin: 20px 0;
            padding: 15px;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <h1>Payment Page</h1>
        <p>Complete your payment by clicking the button below</p>
        
        <div id="message" class="message" style="display: none;"></div>
        
        <button id="payBtn" class="btn btn-success">Complete Payment</button>
        
        <p><small>This is a demo payment page. In production, integrate with real payment systems.</small></p>
    </div>

    <script>
        const paymentUrl = '{{ $paymentUrl }}';
        const payBtn = document.getElementById('payBtn');
        const messageDiv = document.getElementById('message');

        payBtn.addEventListener('click', async () => {
            try {
                payBtn.disabled = true;
                payBtn.textContent = 'Processing...';
                
                const response = await fetch(`/api/payment/${paymentUrl}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    messageDiv.className = 'message success';
                    messageDiv.textContent = data.message || 'Payment completed successfully!';
                    messageDiv.style.display = 'block';
                    payBtn.textContent = 'Payment Completed';
                    payBtn.className = 'btn';
                    payBtn.style.backgroundColor = '#6c757d';
                } else {
                    throw new Error(data.message || 'Payment failed');
                }
            } catch (error) {
                messageDiv.className = 'message error';
                messageDiv.textContent = error.message;
                messageDiv.style.display = 'block';
                payBtn.disabled = false;
                payBtn.textContent = 'Complete Payment';
            }
        });
    </script>
</body>
</html>