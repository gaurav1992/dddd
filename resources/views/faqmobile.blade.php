@extends('frontend.commonapp')
@section('content')
<div class="container-fluid no-padding" style="margin-top:0px" id="inner-header"> <img src="{!! asset('public/images/form-head.jpg') !!}" alt="test" class="img-responsive">
	<div class="carousel-caption"> </div>
	<h3 class="page-heading">F.A.Q.</h3>
</div>
<!--  SECTION-1 -->
<section>
	<div class="container" id="page-faq" style=" min-height: 45vh;">	
		<div class="col-md-12">
			<div class="tabbable" id="tabs-1">			
				
				<nav class="navbar col-sm-3">
					  <div class="navbar-header">
						<button data-target=".map" data-toggle="collapse" class="navbar-toggle" type="button">
						  <span class="icon-bar"></span>
						  <span class="icon-bar"></span>
						  <span class="icon-bar"></span>
						</button>
					  </div>
					  <div class="navbar-collapse map collapse in" aria-expanded="true" style="">
						<ul class="nav nav-tabs navbar-nav ">
							<li class="active"><a data-toggle="tab" href="#panel-1" aria-expanded="false">Services</a></li>
							<li><a data-toggle="tab" href="#panel-2">Payment</a></li>
							<li class=""><a data-toggle="tab" href="#panel-3" aria-expanded="false">Account</a></li>
							<li class=""><a data-toggle="tab" href="#panel-4" aria-expanded="true">Driver</a></li>
							<li><a data-toggle="tab" href="#panel-5">Application</a></li>
							<li><a data-toggle="tab" href="#panel-6">Registering</a></li>
							<li><a data-toggle="tab" href="#panel-7">Promotions</a></li>
						</ul>				
					  </div>
				</nav>
				
				
				
				<div class="tab-content col-sm-9">
					<div class="tab-pane active" id="panel-1">
						<h3 >Services</h3>
							<div class="question">
								<h4>Can I request an DeziNow at any time?</h4>
								<p>Yes. DeziNow operates 24/7. That said, more drivers will be online during busy periods. It's not unusual to find no drivers available in the middle of the night in less populated areas. </p>
							</div>
							
							
							<div class="question">
								<h4>How can I verify that the person who showed up is my DeziNow driver?</h4>
								<p>Ask the driver "Who are you here to pick up?" Your real driver will know the name on your DeziNow account. You can also check the person against their picture in the driver profile in the app.</p>
							</div>	
							
							<div class="question">
								<h4>Can I book a ride in advance?</h4>
								<p>There is no official way to reserve a ride ahead of time. If you get to know a driver, you can ask for a phone number so you can reserve a ride verbally, then send the request through the DeziNow app once they arrive.</p>
							</div>	
							
							<div class="question">
								<h4>How are drivers vetted?</h4>
								<p>DeziNow runs criminal background and MVR (driving record) checks on drivers registered with the app.</p>
							</div>	
							
							<div class="question">
								<h4>Can I book a car from the DeziNow website instead of the app?</h4>
								<p>Not at this time. Feel free to let us know if this is a service you would like.</p>
							</div>	
							
							<div class="question">
								<h4>Can I request a ride for someone without an DeziNow account?</h4>
								<p>Yes. Enter your friend's location when requesting the ride. After the ride is accepted, phone the driver to let him know who to expect. You will be held liable for adhering to the terms and agreement.</p>
							</div>	
							
							<div class="question">
								<h4>Can I set up a round trip with DeziNow? </h4>
								<p>No, you cannot book your ride back in advance. You'll need to order a second car on the way back, or pay your first driver to wait outside if you're making a quick stop.</p>
							</div>	
							
							<div class="question">
								<h4>How long before my appointment time do I need to call DeziNow?</h4>
								<p>When you view the app it shows how far away your closest driver is at that time, gauge accordingly. If you're in a highly populated area you only need request 5-10 min in advance of the eta. In rural areas there may not be a driver available so you should have a back-up plan.</p>
							</div>	
							
							<div class="question">
								<h4>Can I request a specific driver?</h4>
								<p>No. The only way to do this is to ask a driver for a personal phone number, so you can arrange rides over the phone. He may decline to give it to you.</p>
							</div>	
							
							<div class="question">
								<h4>Can I use my DeziNow account while traveling?</h4>
								<p>Yes, but you will have to have data access.</p>
							</div>	
							
							<div class="question">
								<h4>Can the driver pick up at two locations and drop off at one destination? How is this charged?</h4>
								<p>It is up to the driver; the driver can pick up at two separate locations and drop off at one destination. This is charged as a single trip and each pickup doesn't count as a separate pickup. Only the initial pickup is charged.</p>
							</div>	
							
							<div class="question">
								<h4>How will I find my DeziNow driver in a crowd?</h4>
								<p>The app will show you a picture of the driver and the driver will have your car information. Look for a badge that identifies the driver.</p>
							</div>
							<div class="question">
								<h4>Can I use DeziNow from a regular mobile phone or do I need a smart phone?</h4>
								<p>You need to have the DeziNow app in order to request a driver.</p>
							</div>
							<div class="question">
								<h4>Can I request a ride with a phone call?</h4>
								<p>No, you must use the DeziNow app.</p>
							</div>
							<div class="question">
								<h4>Can I tell the driver something before I get picked up?</h4>
								<p>Yes. You actually have the option of texting or phoning your driver. All of this can be done from the app that provides a way to contact your driver.</p>
							</div>
							<div class="question">
								<h4>How do I find out what was my payment yesterday afternoon. I thought I get it on my email but I did not.</h4>
								<p>You should receive an email receipt, however, once the driver has ended your trip, you will also see your total charge in the app under “trip history”.</p>
							</div>
							<div class="question">
								<h4>Is there a way to contact DeziNow for a ride if I cannot download the app on my phone?</h4>
								<p>No, the only way to access DeziNow is through the app.</p>
							</div>
							
							<div class="question">
								<h4> Can I request ride hours in advance and get an estimated fee?</h4>
								<p>You can get an estimated fee, but you can't request a ride in advance. It has to be at the specific moment you want to ride it.</p>
							</div>
							
							<div class="question">
								<h4>Do the drivers have insurance?</h4>
								<p>DeziNow requires that all drivers carry the required insurance. We also require that the customer have insurance that covers their vehicles that allows others to drive their vehicles.</p>
							</div>
							
							<div class="question">
								<h4>Is there a limit to the distance an DeziNow driver will go?</h4>
								<p>Yes. The DeziNow service is not meant for extreme distances and will limit the distance dependent on the location. The limit is set in the app so that any request exceeding the limit will not be accepted.</p>
							</div>
							<div class="question">
								<h4>How do I cancel a ride request?</h4>
								<p>The cancel option should appear in your app after your request a ride. You will be charged a fee if you cancel the request more than 2 minutes after requesting the service.</p>
							</div>
							
							<div class="question">
								<h4> I'm a female and I'm wondering if I can request a female driver.</h4>
								<p>No, there is no way to request a male or female driver.</p>
							</div>	
							<div class="question">
								<h4>How do I end the DeziNow trip?</h4>
								<p>Only the driver can end the trip. If you think the driver made an error, contact DeziNow customer support.</p>
							</div>	
							<div class="question">
								<h4>Is DeziNow available in my city?</h4>
								<p>Visit the DeziNow.com cities page to find out.	</p>
							</div>	
							<div class="question">
								<h4>How can I let my family monitor my location while riding in DeziNow?</h4>
								<p>If you have an iPhone or an Apple product, you can turn on Find my iPhone and your family can monitor where you are.	</p>
							</div>	
							<div class="question">
								<h4>How do I provide an access code for the driver to enter my neighborhood gate?</h4>
								<p>Its best to just text the driver once the trip request has been picked up and the driver is in route so that they know the code once they arrive at the gate.</p>
							</div>					
					</div>					
					
					<div class="tab-pane" id="panel-2">
						<h3>Payment</h3>
						
							<div class="question">
								<h4> Can I use a debit card for DeziNow payments? </h4>
								<p>Not all debit cards are compatible with the billing system. If you get an error message while trying to add the info, you'll need to find a different option. </p>
							</div>
							
							<div class="question">
								<h4>How does the DeziNow payment method work?</h4>
								<p>Visit the main menu of the DeziNow app and select the Payment option or credit card icon to enter your payment information. Once you input and save a credit card, PayPal, or other payment info in the app, all fee payments happen automatically.</p>
							</div>
							
							<div class="question">
								<h4>How can I call for two or more DeziNow drivers?</h4>
								<p>Each DeziNow account can only request one driver. Someone else in your party will need to order the second driver from another DeziNow account. </p>
							</div>
							
							<div class="question">
								<h4>How do I get the cheapest DeziNow ride?	</h4>
								<p>The price is the same no matter which driver picks you up. Prices may vary during certain times, so delaying your journey to a less popular travel time can save money.</p>
							</div>
							
							<div class="question">
								<h4>Will the driver give me a receipt?	</h4>
								<p>The driver will send you an electronic receipt. You can view all your past receipts at the DeziNow.com trip history page. </p>
							</div>
							
							<div class="question">
								<h4>Can i use my card for a family member?	</h4>
								<p>You certainly can. For example, if you wanted to order a driver for your elderly mother, just put in her address as the pick up location, then call or text the driver with her description.</p>
							</div>
							
							<div class="question">
								<h4>How do I update my account if my credit card details have change?	</h4>
								<p>Do this directly in the DeziNow app on your phone. Hit the menu button located top left on the screen. Select "Payment," then select "Add Payment." </p>
							</div>
							
							<div class="question">
								<h4>Does the customer or driver pay tolls?	</h4>
								<p>The customer is responsible for the toll. You should have the bridge toll fee or fast trak ready at the time of service. Otherwise the driver will pay it at the time and add it to your fare at a later time. </p>
							</div>
							
							
							<div class="question">
								<h4>Can I pay a monthly fee for unlimited rides?	</h4>
								<p>No, DeziNow currently only offers individual ride payment. </p>
							</div>
							
							<div class="question">
								<h4>Do I need to tip DeziNow drivers?</h4>
								<p>No. Tipping is not required. However, you are free to tip your driver via the app at the end of the ride.</p>
							</div>
							
							<div class="question">
								<h4>Does DeziNow charge per passenger?</h4>
								<p>No, the cost is the same no matter how many people are in the car. Note that each customer must have a seatbelt.</p>
							</div>
							
							<div class="question">
								<h4>Where do I get an estimation of the fare?</h4>
								<p>Enter your ride details and the fare estimate will appear before you request a ride. Can you use DeziNow through a tablet? The app may work on a tablet, but it is not supported. The app is meant to be utilized on a smartphone. DeziNow customer support may be less likely to give assistance or refunds if you're using an unsupported device. </p>
							</div>
							
					</div>
					
					<div class="tab-pane" id="panel-3">
						<h2>Account</h2>
						
							<div class="question">
								<h4>How do you change the city you are signed up to drive for?	</h4>
								<p>Depending on how far you are moving, you may need to start over again with a new account and none of your old ratings. </p>
							</div>
													
							<div class="question">
								<h4>How do I update my payment information?</h4>
								<p>Visit the DeziNow app main menu, then select Payment or touch the credit card icon. </p>
							</div>
							
							<div class="question">
								<h4>Can I use the same DeziNow account if I change SIM cards?</h4>
								<p>The DeziNow account is linked to your phone number, so you will need to create a new one. If your old and new SIM cards are from the same carrier and country you might be able to request the same phone number to avoid this issue.</p>
							</div>
							
							<div class="question">
								<h4>	</h4>
								<p> </p>
							</div>
						
					</div>
					
					<div class="tab-pane" id="panel-4">
						<h2>Driver</h2>
						
						<div class="question">
							<h4> I'm thinking about earning extra money by becoming an DeziNow driver but I have a few concerns and questions. Who can i talk to about these?	</h4>
							<p>You can always send us an email for any questions and visit our faq section as we are constantly adding new questions and answers.  </p>
						</div>
						
												
						<div class="question">
							<h4>How old do you have to be to become an DeziNow driver?</h4>
							<p>You'll need to be at least 25 years old to drive for DeziNow (and 18 just to use the service). </p>
						</div>
					</div>
					
					
					<div class="tab-pane" id="panel-5">
						<h2>Application</h2>
						<div class="question">
							<h4>How do you set the destination?	</h4>
							<p>After setting the pickup location, the next screen is where to set the destination. </p>
						</div>
					</div>
					
					
					<div class="tab-pane" id="panel-6">
						<h2>Registering</h2>
						<div class="question">
							<h4>Where do I enter the verification code I received as an SMS from DeziNow?</h4>
							<p>You enter the code when you first sign up for an account. </p>
						</div>						
					</div>
					
					<div class="tab-pane" id="panel-7">
						<h2>Promotions</h2>
						<div class="question">
							<h4>Can I use the promo code for DeziNow after I already signed up?</h4>
							<p>DeziNow occasionally give existing users promo codes to celebrate special events, but the vast majority are only for new users.</p>
						</div>	
						
						<div class="question">
							<h4>Where do DeziNow credits come from?</h4>
							<p>Earn DeziNow credits by inviting friends to join DeziNow using the invite code in your app. Once the credits are added to your account, they are automatically deducted to pay for your fares.</p>
						</div>						
					</div>
					
					
				</div>
			</div>
		</div>  
	</div>
</section>
@endsection
