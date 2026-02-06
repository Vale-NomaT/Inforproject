<!DOCTYPE html>
<html>
<head>
    <title>Welcome to SafeRide Kids</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>Welcome, {{ $user->name }}!</h2>
    
    <p>Thank you for joining SafeRide Kids. We are excited to have you on board!</p>
    
    <p>Your account has been successfully created.</p>
    
    @if($user->user_type === 'driver')
        <p>Please note that your driver account is currently <strong>pending approval</strong>. Our team will review your details and documents shortly.</p>
    @else
        <p>You can now log in to your dashboard and start managing your children's trips.</p>
    @endif
    
    <p>If you have any questions, feel free to contact our support team.</p>
    
    <p>Best regards,<br>
    The SafeRide Kids Team</p>
</body>
</html>
