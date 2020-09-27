<!-- Login Area Start -->
<div class="modal fade login-modal" id="login" tabindex="-1" role="dialog" aria-labelledby="login" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <div class="modal-body">
                <div class="logo-area">
                    <img class="logo" src="{{ asset('img/front/SWL_logo-02.png') }}" alt="">
                </div>
                <div class="header-area">
                    <h4 class="title">Great to have you back!</h4>
                    <p class="sub-title">Enter your details below.</p>
                </div>
                <div class="form-area">
                    <div class="alert alert-danger all-error-block" style="display: none;">
                        <ul>
                            <li> asjc asckj caksjc sj sdjkc cjk c</li>
                        </ul>
                    </div>
                    <div class="alert alert-danger success-block" style="display: none;">
                        <ul>
                            <li> asjc asckj caksjc sj sdjkc cjk c</li>
                        </ul>
                    </div>
                    <form  id="user-login-form">
                        @csrf
                        <div class="form-group">
                            <label for="login-email">Email*</label>
                            <input type="email" class="input-field" id="login-email" name="email"  placeholder="Enter your Email">
                        </div>
                        <div class="form-group">
                            <label for="login-password">Password*</label>
                            <input type="password" class="input-field" id="login-password" name="password" placeholder="Password">
                        </div>
                        <div class="form-group">
                            <div class="box">
                                <div class="left">
                                    <input type="checkbox" class="check-box-field" id="input-save-password" checked>
                                    <label for="input-save-password">Remember Password</label>
                                </div>
                                <div class="right">
                                    <a href="javascript:void(0);" class="forgot-password-btn">
                                        Forgot Password?
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="mybtn1 login-btn">Log In</button>
                        </div>
                    </form>
                </div>
                <div class="form-footer">
                    <p>Not a member?
                        <a href="javascript:void(0);" class="create-account-btn">Create account <i class="fas fa-angle-double-right"></i></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Login Area End -->

<!-- register Area Start -->
<div class="modal fade login-modal sign-in" id="signin" tabindex="-1" role="dialog" aria-labelledby="signin" aria-hidden="true" data-backdrop="static" data-keyboard="false">

    <div class="modal-dialog modal-dialog-centered " role="document">
    
        <div class="modal-content">
     
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <div class="modal-body">
                <div class="logo-area">
                    <img class="logo" src="{{ asset('img/front/SWL_logo-02.png') }}" alt="">
                </div>
                <div class="header-area">
                    <h4 class="title">Great to have you onboard!</h4>
                    <p class="sub-title">Enter your details below and get started.</p>
                </div>
                <div class="form-area">
                    <form id="user-register-form">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name*</label>
                            <input type="text" class="input-field" id="name" value="{{ old('name') }}"  name="name" placeholder="Enter your Name">
                        </div>
                        <div class="form-group">
                            <label for="email">Email*</label>
                            <input type="email" class="input-field" id="email" value="{{ old('email') }}" name="email" autocomplete="off"  placeholder="Enter your Email">
                        </div>
                        <div class="form-group">
                            <label for="password">Password*</label>
                            <input type="password" class="input-field" id="password" name="password" value="" autocomplete="off" placeholder="Enter your password">
                        </div>
                        <div class="form-group">
                            <label for="type">Type*</label>
                            <input type="text" class="input-field" id="type" name="type" placeholder="Enter your Type">
                        </div>
                        {{-- <div class="form-group">
                            <label for="confirm_pass">Referral Code (Optional)</label>
                            <input type="text" class="input-field" id="referral" name="referral" placeholder="Enter Referral Code (Optional)">
                        </div> --}}
                        {{-- <div class="form-group">
                            <div class="check-group">
                                <input type="checkbox" class="check-box-field" id="terms" name="terms" checked>
                                <label for="terms">
                                    I agree with <a href="javascript:void(0)">Terms and Conditions</a> and  <a href="javascript:void(0)">Privacy Policy</a>
                                </label>
                                <label id="terms-error" class="error" style="display:none;" for="terms">This field is required.</label>
                            </div>
                        </div> --}}
                        <div class="form-group">
                            <button type="submit" class="mybtn1 user-register-btn">Register</button>
                        </div>
                    </form>
                </div>

                <div class="form-footer">
                    <p>Already Have an Account!
                        <a href="javascript:void(0);" class="user-login-btn">Click here to Login <i class="fas fa-angle-double-right"></i></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Login Area End -->


