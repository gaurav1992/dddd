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
							<li><a data-toggle="tab" href="#panel-8">Terms & Conditions</a></li>
						</ul>				
					  </div>
				</nav>
				
				
				
				<div class="tab-content col-sm-9">
					<div class="tab-pane" id="panel-1">
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
					
					
					<div class="tab-pane active" id="panel-8">
						<h2>Terms & Conditions</h2>
						
<div class="question">
	<h4>TERMS OF SERVICE AGREEMENT DRIVER</h4>

	<p>PLEASE READ THE FOLLOWING TERMS OF SERVICE AGREEMENT CAREFULLY. BY ACCESSING, USING OUR
	SITES OR APP AND OUR SERVICES OR BY REGISTERING AS A CUSTOMER, YOU HEREBY AGREE TO BE
	BOUND BY THE TERMS AND ALL TERMS INCORPORATED HEREIN BY REFERENCE. IT IS THE
	RESPONSIBILITY OF YOU, THE USER, CUSTOMER, OR PROSPECTIVE CUSTOMER TO READ THE TERMS AND
	CONDITIONS BEFORE PROCEEDING TO USE THIS SITE AND APP. IF YOU DO NOT EXPRESSLY AGREE TO
	ALL OF THE TERMS AND CONDITIONS, THEN PLEASE DO NOT ACCESS OR USE OUR SITES OR OUR APP OR
	OUR SERVICES. THIS TERMS OF SERVICE AGREEMENT IS EFFECTIVE AS OF 01/01/2016.</p>
</div>

<div class="question">
<h4>ACCEPTANCE OF TERMS</h4>

<p>The following Terms of Service Agreement (the "TOS") is a legally binding agreement that shall govern
the relationship with our users and others which may interact or interface with DeziNow, Inc., also
known as DeziNow, located at 1255 Treat Blvd Suite 300, Walnut Creek, California 94597 and our
subsidiaries and affiliates, in association with the use of the DeziNow website and app, which includes
www.DeziNow.com, (the "Site") and its Services, which shall be defined below. For the purpose of this
contract, also herein referred to as "Contract", "Terms and Conditions", or "Agreement", any reference to
"you" or "your" within this document shall refer to the driver, member, and user utilizing the DeziNow
website, software, mobile application or app or application, and referral services. Any references to "we",
"us", or "our" shall refer solely to DeziNow.</p>
</div>


<div class="question">
<h4>DESCRIPTION OF SERVICES OFFERED</h4>

<p>The DeziNow Site and App is a rideshare referral service platform which has the following description:

DeziNow is a platform for referral services where information about the Customer and the services
required are shared with the Drivers who are offering driving services to those Customers. By signing up
with and utilizing the Service, the Driver agrees with this Agreement and agrees to comply with all the
terms and conditions of this Agreement. Driver agrees that DeziNow is only a referral service that will
inform Drivers about individuals that requires services that you may provide. You acknowledge and
agree that the Drivers utilizing and on DeziNow are independent contractors and are not employees of
DeziNow. You agree that your availability and acceptance of requests is at your discretion. DeziNow will
require a referral fee and compliance with the TOS. Requests for a Driver and rates are not guaranteed
but dependent upon the market and Customers.
<br/><br/>
Any and all visitors to our site, despite whether they are registered or not, shall be deemed as "users" of
the herein contained Services provided for the purpose of this TOS. Once an individual register's for our
Services, through the process of creating an account, the user shall then be considered a "member."
<br/><br/>
The user and/or member acknowledges and agrees that the Services provided and made available
through our website and applications, which may include some mobile applications and that those
applications may be made available on various social media networking sites and numerous other
<br/><br/>
platforms and downloadable programs, are the sole property of DeziNow, Inc.. At its discretion,
DeziNow, Inc. may offer additional website Services and/or products, or update, modify or revise any
current content and Services, and this Agreement shall apply to any and all additional Services and/or
products and any and all updated, modified or revised Services unless otherwise stipulated. DeziNow,
Inc. does hereby reserve the right to cancel and cease offering any of the aforementioned Services
and/or products. You, as the end user and/or member, acknowledge, accept and agree that DeziNow,
Inc. shall not be held liable for any such updates, modifications, revisions, suspensions or discontinuance
of any of our Services and/or products. Your continued use of the Services provided, after such posting
of any updates, changes, and/or modifications shall constitute your acceptance of such updates,
changes and/or modifications, and as such, frequent review of this Agreement and any and all applicable
terms and policies should be made by you to ensure you are aware of all terms and policies currently in
effect. Should you not agree to the updated, revised or modified terms, you must stop using the
provided Services forthwith.
<br/><br/>
Furthermore, the user and/or member understands, acknowledges and agrees that the Services offered
shall be provided "AS IS" and as such DeziNow, Inc. shall not assume any responsibility or obligation for
the timeliness, missed delivery, deletion and/or any failure to store user content, communication or
personalization settings.
</p>
</div>

<div class="question">
<h4>REGISTRATION</h4>

<p>To register and become a "member" of the Site, you must be at least 18 years of age to enter into and
form a legally binding contract. In addition, you must be in good standing and not an individual that has
been previously barred from receiving DeziNow's Services under the laws and statutes of the United
States or other applicable jurisdiction.
<br/><br/>
When you register, DeziNow may collect information such as your name, e-mail address, birth date,
gender, mailing address, occupation, industry and personal interests. You can edit your account
information at any time. Once you register with DeziNow and sign in to our Services, you are no longer
anonymous to us.
<br/><br/>
Furthermore, the registering party hereby acknowledges, understands and agrees to:
<br/><br/>
a) furnish factual, correct, current and complete information with regards to yourself as may be
requested by the data registration process, and
<br/><br/>
b) maintain and promptly update your registration and profile information in an effort to maintain
accuracy and completeness at all times.
<br/><br/>
If anyone knowingly provides any information of a false, untrue, inaccurate or incomplete nature,
DeziNow, Inc. will have sufficient grounds and rights to suspend or terminate the member in violation of
this aspect of the Agreement, and as such refuse any and all current or future use of DeziNow, Inc.
Services, or any portion thereof.
</p>
</div>

<div class="question">
<h4>PRIVACY POLICY</h4>
<p>
Every member's registration data and various other personal information are strictly protected by the
DeziNow, Inc. Online Privacy Policy (see the full Privacy Policy at www.dezinow.com/privacy). As a
member, you herein consent to the collection and use of the information provided, including the
transfer of information within the United States and/or other countries for storage, processing or use by
DeziNow, Inc. and/or our subsidiaries and affiliates.</p>
</div>

<div class="question">
<h4>MEMBER ACCOUNT, USERNAME, PASSWORD AND SECURITY</h4>

<p>
When you set up an account, you are the sole authorized user of your account. You shall be responsible
for maintaining the secrecy and confidentiality of your password and for all activities that transpire on or
within your account. It is your responsibility for any act or omission of any user(s) that access your
account information that, if undertaken by you, would be deemed a violation of the TOS. It shall be your
responsibility to notify DeziNow, Inc. immediately if you notice any unauthorized access or use of your
account or password or any other breach of security. DeziNow, Inc. shall not be held liable for any loss
and/or damage arising from any failure to comply with this term and/or condition of the TOS.
<br/><br/>
You acknowledge, consent and agree that DeziNow may access, preserve and disclose your account
information if, in our sole discretion, we believe doing so is in any manner necessary to:
<br/><br/>
a)comply with legal process;
<br/><br/>
b)enforce the TOS;
<br/><br/>
c)respond to claims regarding content that violates the legal rights or obligation of third parties which
we contract;
<br/><br/>
d)respond to requests for customer service;
<br/><br/>
e)protect the rights, property, and personal safety of DeziNow, its users and the public.
<br/><br/>
You acknowledge that DeziNow expressly reserves the right to immediately modify, suspend or
terminate your account and refuse current or future user of our online and referral services. You agree
that DeziNow is not required to give prior notice of termination of your account and referral requests.
Instances where DeziNow may suspend, terminate your account, or refuse current or future users of our
online and referral services may include, but not limited to:
<br/><br/>
    a) Violation or attempted violation of legal rights of others to use the services of DeziNow;<br/><br/>
    b) Violation of this agreement;<br/><br/>
    c) Any action which may be harmful to others<br/><br/>

You acknowledge and understand that DeziNow utilizes software and applications that includes security
components that is used to protect, record, and preserve the user's data that is utilized by the operation
of the software and the user's personal privacy. By utilizing DeziNow services and software, you agree
that you shall do nothing to circumvent or override any measures that the software uses or those
<br/><br/>
utilized by DeziNow. For more information regarding the security and privacy controls, please see
DeziNow's Privacy Policy.
</p>
</div>

<div class="question">
<h4>CONDUCT</h4>

<p>As a user or member of the Site, you herein acknowledge, understand and agree that all information,
text, software, data, photographs, music, video, messages, tags or any other content, whether it is
publicly or privately posted and/or transmitted, is the expressed sole responsibility of the individual
from whom the content originated. In short, this means that you are solely responsible for any and all
content posted, uploaded, emailed, transmitted or otherwise made available by way of the DeziNow
Services, and as such, we do not guarantee the accuracy, integrity or quality of such content. It is
expressly understood that by use of our Services, you may be exposed to content including, but not
limited to, any errors or omissions in any content posted, and/or any loss or damage of any kind
incurred as a result of the use of any content posted, emailed, transmitted or otherwise made available
by DeziNow.
<br/><br/>
Furthermore, you herein agree not to make use of DeziNow, Inc.'s Services for the purpose of:
<br/><br/>
a) uploading, posting, emailing, transmitting, or otherwise making available any content that shall be
deemed unlawful, harmful, threatening, abusive, harassing, tortious, defamatory, vulgar, obscene,
libelous, or invasive of another's privacy or which is hateful, and/or racially, ethnically, or otherwise
objectionable;
<br/><br/>
b) causing harm to minors in any manner whatsoever;
<br/><br/>
c) impersonating any individual or entity, including, but not limited to, any DeziNow officials, forum
leaders, guides or hosts or falsely stating or otherwise misrepresenting any affiliation with an individual
or entity;
<br/><br/>
d) forging captions, headings or titles or otherwise offering any content that you personally have no
right to pursuant to any law nor having any contractual or fiduciary relationship with;
<br/><br/>
e) uploading, posting, emailing, transmitting or otherwise offering any such content that may infringe
upon any patent, copyright, trademark, or any other proprietary or intellectual rights of any other party;
<br/><br/>
f) uploading, posting, emailing, transmitting or otherwise offering any content that you do not
personally have any right to offer pursuant to any law or in accordance with any contractual or fiduciary
relationship;
<br/><br/>
g) uploading, posting, emailing, transmitting, or otherwise offering any unsolicited or unauthorized
advertising, promotional flyers, "junk mail," "spam," or any other form of solicitation, except in any such
areas that may have been designated for such purpose;
<br/><br/>
h) uploading, posting, emailing, transmitting, or otherwise offering any source that may contain a
software virus or other computer code, any files and/or programs which have been designed to
<br/><br/>
interfere, destroy and/or limit the operation of any computer software, hardware, or
telecommunication equipment;
<br/><br/>
i) disrupting the normal flow of communication, or otherwise acting in any manner that would
negatively affect other users' ability to participate in any real time interactions;
<br/><br/>
j) interfering with or disrupting any DeziNow, Inc. Services, servers and/or networks that may be
connected or related to our website, including, but not limited to, the use of any device software and/or
routine to bypass the robot exclusion headers;
<br/><br/>
k) intentionally or unintentionally violating any local, state, federal, national or international law,
including, but not limited to, rules, guidelines, and/or regulations decreed by the U.S. Securities and
Exchange Commission, in addition to any rules of any nation or other securities exchange, that would
include without limitation, the New York Stock Exchange, the American Stock Exchange, or the NASDAQ,
and any regulations having the force of law;
<br/><br/>
l) providing informational support or resources, concealing and/or disguising the character, location, and
or source to any organization delegated by the United States government as a "foreign terrorist
organization" in accordance to Section 219 of the Immigration Nationality Act;
<br/><br/>
m) "stalking" or with the intent to otherwise harass another individual; and/or
<br/><br/>
n) collecting or storing of any personal data relating to any other member or user in connection with the
prohibited conduct and/or activities which have been set forth in the aforementioned paragraphs.
<br/><br/>
Users must be of legal age according to where they are in order to submit personal information. Users
agree that they are aware of the legal age requirements of going into any agreement and that they are
of legal age according to the law which governs their jurisdiction to enter into any contract or
agreement with DeziNow.
<br/><br/>
You agree to act in a professional and responsible manner in performance of all driving services referred
by DeziNow.
<br/><br/>
You agree that you are solely responsible for any actions while visiting and utilizing DeziNow.com and
the application and will comply with all applicable local, state, federal and foreign laws, rules and
regulations regarding the internet and United States copyright laws and export regulations.
<br/><br/>
DeziNow, Inc. herein reserves the right to pre-screen, refuse and/or delete any content currently
available through our Services. In addition, we reserve the right to remove and/or delete any such
content that would violate the TOS or which would otherwise be considered offensive to other visitors,
users and/or members.
<br/><br/>
DeziNow, Inc. herein reserves the right to access, preserve and/or disclose member account information
and/or content if it is requested to do so by law or in good faith belief that any such action is deemed
reasonably necessary for:
<br/><br/>
a) compliance with any legal process;
<br/><br/>
b) enforcement of the TOS;
<br/><br/>
c) responding to any claim that therein contained content is in violation of the rights of any third party;
<br/><br/>
d) responding to requests for customer service; or
<br/><br/>
e) protecting the rights, property or the personal safety of DeziNow, Inc., its visitors, users and members,
including the general public.
<br/><br/>
DeziNow, Inc. herein reserves the right to include the use of security components that may permit
digital information or material to be protected, and that such use of information and/or material is
subject to usage guidelines and regulations established by DeziNow, Inc. or any other content providers
supplying content services to DeziNow, Inc.. You are hereby prohibited from making any attempt to
override or circumvent any of the embedded usage rules in our Services. Furthermore, unauthorized
reproduction, publication, distribution, or exhibition of any information or materials supplied by our
Services, despite whether done so in whole or in part, is expressly prohibited.
<br/><br/>
You agree and acknowledge that any driving services for customers registered with DeziNow will be
done through your contractual relationship with DeziNow and the DeziNow website or application. You
must not contact the Customer directly for requests for services. You agree that any contact directly by
the Customer for services will be reported to DeziNow immediately. Failure to report a Customer that
contacts you directly for services is a material breach of this Agreement.You agree that driver services
received outside of the DeziNow software and application is a breach of this agreement and you hereby
waive any rights and protections provided by this agreement and you waive any rights or remedies you
have against DeziNow under this agreement or under applicable law. Violation of the agreement and
contacting with the Customers directly for services may lead to:
<br/><br/>
     a) Suspension or termination of your account and future service requests;<br/><br/>
     b) Pursuing remedy , including but not limited to, lost profits, costs, expenses, and any fees
         necessary to enforce this provision in a court of law and recovering any damages related to each
         incident;
</p>
</div>

<div class="question">
<h4>Driver payments</h4>
<p>
DeziNow is a referral service platform where customers may request for drivers, and where drivers may
find customers requesting their services for a set price. You agree and accept that DeziNow will collect
all fees and payments for the services requested by the customer and then pass the payments onto the
drivers after taking out a certain percentage of the payment as part of the referral fee charged by
DeziNow. You agree that DeziNow shall:
<br/><br/>
     a) Send you requests from Customers for drive requests.
<br/><br/>
     b) Automatically and immediately bill and collect for any services provided via the billing options
         chosen on the DeziNow website or app right after the services or attempt of the service has
         been performed.<br/><br/>
     c) Bill the Customer a pickup fee for the amount spent by the driver to get to the customer
         requesting a driver. However, you agree and acknowledge that DeziNow does not guarantee
         that the full amount you spend getting to the customer will be collected for the pickup fee.<br/><br/>
     d) Not be responsible for any parking fees or tolls or tickets acquired during the requested driver
         service. You will be responsibility for paying all fees and payments associated, such as, but not
         limited to, tolls and parking fees you incur while driving. Tolls and parking (if required should be
         asked for from the Customer. Customer has agreed to pay for any and all fees and payments
         required with driving the vehicle to the designated location.)<br/><br/>
     e) Not be responsible for costs or fees incurred for the driver to get to the customer locations.
         Customer will be responsible and charged for fees and payments incurred and associated with
         the driver going to customer location such as, but not limited to, the pick up fee. DeziNow will
         pass on those fees to the Driver.<br/><br/>
     f) Pay to the driver based on the amount charged to the customer minus the DeziNow percentage,
         on a weekly basis.<br/><br/>
     g) Collect from customer a minimum fee and/or pickup fee if the customer is a "no show" and you
         have waited at least 15 minutes at the pick up location and made an effort to located the
         customer. The pickup fee will be given to the driver if the customer is a "no show" after 15
         minutes.<br/><br/>
     h) Not charge the customer any fees or a minimum charge if the driver cancels the pickup. Driver
         will not receive any compensation if the driver cancels the pick up at any point in time.<br/><br/>
     i) Have the right to alter the service and pickup fees at any time, without prior notice to you, for
         referrals through the DeziNow services.<br/><br/>
     j) Charge for all services in U.S. Dollars.<br/><br/>

Your total compensation will be the amount charged to the customer minus a DeziNow service fee and
20% of the calculated bill after the service has been performed or canceled by the customer. The
driver's compensation shall be provided to the Driver in the manner of a check or direct deposit. The
check and direct deposit can take up to 14 business days after the date that DeziNow receives the
amount from the customer.
<br/><br/>
You acknowledge and agree that DeziNow may alter the rate of compensation and rates charged to the
customers without any prior notice to you and to any future referrals under this Agreement. DeziNow at
its sole discretion reserves the complete right to withhold or refuse payment to any Driver in partial or
in full and may do so for any reason and at any time. DeziNow reserves the right to withhold all or any
part of the compensation owed to the Driver for prior services performed as liquidated damages for the
purposes of defraying DeziNow's expenses and lost business incident for any incidence where the Drive
to follow through and complete the services of the referral service after accepting the referral. The
withholding of compensation from the Driver is in addition to, and not in lieu of, DeziNow's other rights
and remedieis hereunder or under applicable law.
</p>
</div>

<div class="question">
<h4>PROMOTIONS/ REFERALS/ CREDITS/ REWARDS</h4>
<p>
DeziNow, in its sole discretion, expressly reserves the right to immediately modify, suspend or terminate
any promotions, credits, referral credits, or rewards given. Referral codes given to Drivers may be used
by the Drivers specified by DeziNow and not utilized for other purposes other than the intended purpose
without the expressed written permission of DeziNow.
<br/><br/>
DeziNow may utilize and inform Drivers of promotions and rewards via email, text, or other forms of
communications. The promotional codes and rewards programs may not be used except as intended.
<br/><br/>
DeziNow is not required to give you any prior notice of modification, suspension, or termination of the
promotions, credits, or rewards given by DeziNow.
</p>
</div>


<div class="question">
<h4>CANCELLATIONS</h4>
<p>
You acknowledge and agree that any requests for driver services cancelled by the Customer within 2
minutes after acceptance of the request will not be charged a minimum cancellation fee and not
charged any pickup fees that may be incurred by the Driver. You acknowledge and agree that
cancellations must be done via the app and that any cancellations by the driver will not incur any
charges for the Customer regardless of the time that has passed. You acknowledge and agree that
cancellation of a Driver request after accepting the request may lead to lower rating and impact future
promotional statuses at DeziNow which may lead to a lower rate of requests. Inability to cancel request
due to application issues must be addressed by contacting us directly via the customer@dezinow.com
email address within 24 hours after the ride request has been made.
</p>
</div>
<div class="question">
	
<h4>LIABILITY/INSURANCE</h4>
<p>
You are required to maintain and carry proof of your respective state's minimum required motor vehicle
insurance coverage which covers you as the driver for any vehicles you may drive. You agree to provide
a copy of your driver's license and proof of insurance to DeziNow prior to receiving referrals. You agree
and acknowledge that due to the inherent risks associated with driving a vehicle, accidents may occur
and that there are risks of incidents occurring. Your insurance must cover the vehicle that is to be used
for the driver services.
<br/><br/>
You agree to provide proof of insurance to the customer prior to providing services. You agree to
contact DeziNow immediately and cease any and all services through DeziNow if there is an issue with
your insurance coverage such as, but not limited to, cancellation of the policy, lapse in coverage, or
failure to purchase automobile insurance.
<br/><br/>
You acknowledge and agree that DeziNow is a referral service only and are not responsible for ensuring
that the customer has all the necessary paperwork and documents such as, but not limited to, proof of
<br/><br/>
insurance and current registration. You agree that you will be responsible for visually inspecting the
Customer's current insurance and registration for the vehicle.
<br/><br/>
You acknowledge and agree that you are responsible for inspecting the Customer's vehicle for any
scratches, dents, or any preexisting damages prior to beginning the trip. You agree to make a written
record prior to the trip and bring to the Customer's attention and provide a copy to the Customer before
starting the trip.
<br/><br/>
DeziNow has general liability and automobile insurance which may be implemented in case of an
accident caused by the driver referred by the DeziNow services. For any claims under the DeziNow
coverage, requests must be submitted, along with an incident report and a police report within 7 days
after the accident. The police report must indicate that the driver referred by DeziNow was at fault.
<br/><br/>
You agree and acknowledge that not all incidents will be covered under the insurance carried by
DeziNow and that you or the Customer may be personally held liable for damages and restitutions that
the insurance does not cover.
<br/><br/>
Customer has the right refuse services if you cannot show proof of current insurance coverage which
will not incur any fees nor a minimum charge for the ride request. Any trips without proof of insurance is
a breach of this agreement.
</p>
</div>


<div class="question">
	
<h4>Independent Contractor Drivers</h4>
<p>
You acknowledge and agree that you and all Drivers on the DeziNow website and application are
independent contractors and are not employees of DeziNow. DeziNow is only a referral service that
connects customers requesting drivers with independent contractors who wish to provide those services.
You agree that this agreement and relationship does not create any actual or apparent agency,
partnership, franchise, or relationship of employer and employee between you (the driver) and DeziNow.
<br/><br/>
You acknowledge that any agreements made with other parties outside this agreement are not
authorized by DeziNow and are not enforceable. The driver is contractually bound not to represent
themselves s an agent or legal representative of DeziNOw and shall not be construed as ever doing so.
You agree that you shall not represent yourself as an agent or legal representative of DeziNow and you
are not authorized to enter into or commit DeziNow to any agreements.
<br/><br/>
You acknowledge that as a Independent contractor, you are not entitled to participate in any of the
Company's benefits, including but not limited to health or retirement plans, reimbursements, or other
benefits outside this Agreement.
<br/><br/>
You acknowledge and agree that as an independent contractor, you are responsible for all taxes and
withholdings associated with services rendered and may be compensated for. DeziNow will not be liable
for any taxes, workman's compensation, unemployment insurance and taxes, employer's liability,
<br/><br/>
employer's FICA, social security, withholding tax, or other taxes for or on behalf of you or anyone else
performing services under this agreement.
<br/><br/>
DeziNow may change, suspend, or discontinue any feature, services, or aspect of the services available
at DeziNow at any time. DeziNow may add, remove, or modify any content of DeziNow.com or
application , including that of third parties, at any time.
</p>
</div>

<div class="question">

<h4>CAUTIONS FOR GLOBAL USE AND EXPORT AND IMPORT COMPLIANCE</h4>

<p>Due to the global nature of the internet, through the use of our network you hereby agree to comply
with all local rules relating to online conduct and that which is considered acceptable Content.
Uploading, posting and/or transferring of software, technology and other technical data may be subject
to the export and import laws of the United States and possibly other countries. Through the use of our
network, you thus agree to comply with all applicable export and import laws, statutes and regulations,
including, but not limited to, the Export Administration Regulations
<span class="breakWord">(http://www.access.gpo.gov/bis/ear/ear_data.html)</span>, as well as the sanctions control program of the
United States  <span class="breakWord">(http://www.treasury.gov/resourcecenter/sanctions/Programs/Pages/Programs.aspx)</span>.
Furthermore, you state and pledge that you:
<br/><br/>
a) are not on the list of prohibited individuals which may be identified on any government export
exclusion report <span class="breakWord">(http://www.bis.doc.gov/complianceandenforcement/liststocheck.htm)</span> nor a member
of any other government which may be part of an export-prohibited country identified in applicable
export and import laws and regulations;
<br/><br/>
b) agree not to transfer any software, technology or any other technical data through the use of our
network Services to any export-prohibited country;
<br/><br/>
c) agree not to use our website network Services for any military, nuclear, missile, chemical or biological
weaponry end uses that would be a violation of the U.S. export laws; and d) agree not to post, transfer
nor upload any software, technology or any other technical data which would be in violation of the U.S.
or other applicable export and/or import laws.
</p>
</div>

<div class="question">
	
<h4>CONTENT PLACED OR MADE AVAILABLE FOR COMPANY SERVICES</h4>
<p>
DeziNow, Inc. shall not lay claim to ownership of any content submitted by any visitor, member, or user,
nor make such content available for inclusion on our website Services. Therefore, you hereby grant and
allow for DeziNow, Inc. the below listed worldwide, royalty-free and non-exclusive licenses, as
applicable:
<br/><br/>
a) The content submitted or made available for inclusion on the publicly accessible areas of DeziNow,
Inc.'s sites, the license provided to permit to use, distribute, reproduce, modify, adapt, publicly perform
and/or publicly display said Content on our network Services is for the sole purpose of providing and
promoting the specific area to which this content was placed and/or made available for viewing. This
<br/><br/>
license shall be available so long as you are a member of DeziNow, Inc.'s sites, and shall terminate at
such time when you elect to discontinue your membership.
<br/><br/>
b) Photos, audio, video and/or graphics submitted or made available for inclusion on the publicly
accessible areas of DeziNow, Inc.'s sites, the license provided to permit to use, distribute, reproduce,
modify, adapt, publicly perform and/or publicly display said Content on our network Services are for the
sole purpose of providing and promoting the specific area in which this content was placed and/or made
available for viewing. This license shall be available so long as you are a member of DeziNow, Inc.'s sites
and shall terminate at such time when you elect to discontinue your membership.
<br/><br/>
c) For any other content submitted or made available for inclusion on the publicly accessible areas of
DeziNow, Inc.'s sites, the continuous, binding and completely sub-licensable license which is meant to
permit to use, distribute, reproduce, modify, adapt, publish, translate, publicly perform and/or publicly
display said content, whether in whole or in part, and the incorporation of any such Content into other
works in any arrangement or medium current used or later developed.
<br/><br/>
Those areas which may be deemed "publicly accessible" areas of DeziNow, Inc.'s sites are those such
areas of our network properties which are meant to be available to the general public, and which would
include message boards and groups that are openly available to both users and members.
</p>
</div>

<div class="question">
	<h4>CONTRIBUTIONS TO COMPANY WEBSITE</h4>

<p>
DeziNow, Inc. provides an area for our users and members to contribute feedback to our website. When
you submit ideas, documents, suggestions and/or proposals ("Contributions") to our site, you
acknowledge and agree that:
<br/><br/>
a) your contributions do not contain any type of confidential or proprietary information;
<br/><br/>
b) DeziNow shall not be liable or under any obligation to ensure or maintain confidentiality, expressed
or implied, related to any Contributions;
<br/><br/>
c) DeziNow shall be entitled to make use of and/or disclose any such Contributions in any such manner
as they may see fit;
<br/><br/>
d) the contributor's Contributions shall automatically become the sole property of DeziNow; and
<br/><br/>
e) DeziNow is under no obligation to either compensate or provide any form of reimbursement in any
manner or nature.
</p>
</div>
<div class="question">
<h4>INDEMNITY</h4>
<p>
All users and/or members herein agree to insure and hold DeziNow, Inc., our subsidiaries, affiliates,
agents, employees, officers, partners and/or licensors blameless or not liable for any claim or demand,
which may include, but is not limited to, reasonable attorney fees made by any third party which may
arise from any content a member or user of our site may submit, post, modify, transmit or otherwise
make available through our Services, the use of DeziNow Services or your connection with these
<br/><br/>
Services, your violations of the Terms of Service and/or your violation of any such rights of another
person.
</p>
</div>



<div class="question">
<h4>COMMERCIAL REUSE OF SERVICES</h4>
<p>
The member or user herein agrees not to replicate, duplicate, copy, trade, sell, resell nor exploit for any
commercial reason any part, use of, or access to DeziNow's sites.
</p>
</div>


<div class="question">
<h4>USE AND STORAGE GENERAL PRACTICES</h4>
<p>
You herein acknowledge that DeziNow, Inc. may set up any such practices and/or limits regarding the
use of our Services, without limitation of the maximum number of days that any email, message posting
or any other uploaded content shall be retained by DeziNow, Inc., nor the maximum number of email
messages that may be sent and/or received by any member, the maximum volume or size of any email
message that may be sent from or may be received by an account on our Service, the maximum disk
space allowable that shall be allocated on DeziNow, Inc.'s servers on the member's behalf, and/or the
maximum number of times and/or duration that any member may access our Services in a given period
of time. In addition, you also agree that DeziNow, Inc. has absolutely no responsibility or liability for the
removal or failure to maintain storage of any messages and/or other communications or content
maintained or transmitted by our Services. You also herein acknowledge that we reserve the right to
delete or remove any account that is no longer active for an extended period of time. Furthermore,
DeziNow, Inc. shall reserve the right to modify, alter and/or update these general practices and limits at
our discretion.
</p>
</div>
<div class="question">
<h4>MODIFICATIONS</h4>
<p>
DeziNow, Inc. shall reserve the right at any time it may deem fit, to modify, alter and or discontinue,
whether temporarily or permanently, our service, or any part thereof, with or without prior notice. In
addition, we shall not be held liable to you or to any third party for any such alteration, modification,
suspension and/or discontinuance of our Services, or any part thereof.
</p>
</div>
<div class="question">
<h4>TERMINATION</h4>
<p>
As a member of www.DeziNow.com, you may cancel or terminate your account, associated email
address and/or access to our Services by submitting a cancellation or termination request to
account@dezinow.com.
<br/><br/>
As a member, you agree that DeziNow, Inc. may, without any prior written notice, immediately suspend,
terminate, discontinue and/or limit your account, any email associated with your account, and access to
any of our Services. The cause for such termination, discontinuance, suspension and/or limitation of
access shall include, but is not limited to:
<br/><br/>
a) any breach or violation of our TOS or any other incorporated agreement, regulation and/or guideline;
<br/><br/>
b) by way of requests from law enforcement or any other governmental agencies;
<br/><br/>
c) the discontinuance, alteration and/or material modification to our Services, or any part thereof;
<br/><br/>
d) unexpected technical or security issues and/or problems;
<br/><br/>
e) any extended periods of inactivity;
<br/><br/>
f) any engagement by you in any fraudulent or illegal activities; and/or
<br/><br/>
g) the nonpayment of any associated fees that may be owed by you in connection with your
www.DeziNow.com account Services.
<br/><br/>
Furthermore, you herein agree that any and all terminations, suspensions, discontinuances, and or
limitations of access for cause shall be made at our sole discretion and that we shall not be liable to you
or any other third party with regards to the termination of your account, associated email address
and/or access to any of our Services.
<br/><br/>
The termination of your account with www.DeziNow.com shall include any and/or all of the following:
<br/><br/>
a) the removal of any access to all or part of the Services offered within www.DeziNow.com;
<br/><br/>
b) the deletion of your password and any and all related information, files, and any such content that
may be associated with or inside your account, or any part thereof; and
<br/><br/>
c) the barring of any further use of all or part of our Services.
</p>
</div>
<div class="question"><h4>
ADVERTISERS</h4><p>

Any correspondence or business dealings with, or the participation in any promotions of, advertisers
located on or through our Services, which may include the payment and/or delivery of such related
goods and/or Services, and any such other term, condition, warranty and/or representation associated
with such dealings, are and shall be solely between you and any such advertiser. Moreover, you herein
agree that DeziNow, Inc. shall not be held responsible or liable for any loss or damage of any nature or
manner incurred as a direct result of any such dealings or as a result of the presence of such advertisers
on our website.
</p></div><div class="question"><h4>
LINKS</h4><p>

Either DeziNow, Inc. or any third parties may provide links to other websites and/or resources. Thus, you
acknowledge and agree that we are not responsible for the availability of any such external sites or
resources, and as such, we do not endorse nor are we responsible or liable for any content, products,
advertising or any other materials, on or available from such third party sites or resources. Furthermore,
you acknowledge and agree that DeziNow, Inc. shall not be responsible or liable, directly or indirectly,
for any such damage or loss which may be a result of, caused or allegedly to be caused by or in
connection with the use of or the reliance on any such content, goods or Services made available on or
through any such site or resource.
</p></div><div class="question"><h4>
PROPRIETARY RIGHTS</h4><p>

You do hereby acknowledge and agree that DeziNow, Inc.'s Services and any essential software that
may be used in connection with our Services ("Software") shall contain proprietary and confidential
material that is protected by applicable intellectual property rights and other laws. Furthermore, you
herein acknowledge and agree that any Content which may be contained in any advertisements or
information presented by and through our Services or by advertisers is protected by copyrights,
trademarks, patents or other proprietary rights and laws. Therefore, except for that which is expressly
permitted by applicable law or as authorized by DeziNow, Inc. or such applicable licensor, you agree not
to alter, modify, lease, rent, loan, sell, distribute, transmit, broadcast, publicly perform and/or created
any plagiaristic works which are based on DeziNow, Inc. Services (e.g. Content or Software), in whole or
part. DeziNow, Inc. herein has granted you personal, non-transferable and non-exclusive rights and/or
license to make use of the object code or our Software on a single computer, as long as you do not, and
shall not, allow any third party to duplicate, alter, modify, create or plagiarize work from, reverse
engineer, reverse assemble or otherwise make an attempt to locate or discern any source code, sell,
assign, sublicense, grant a security interest in and/or otherwise transfer any such right in the Software.
Furthermore, you do herein agree not to alter or change the Software in any manner, nature or form,
and as such, not to use any modified versions of the Software, including and without limitation, for the
purpose of obtaining unauthorized access to our Services. Lastly, you also agree not to access or attempt
to access our Services through any means other than through the interface which is provided by
DeziNow, Inc. for use in accessing our Services.
</p></div><div class="question"><h4>
WARRANTY DISCLAIMERS</h4><p>

YOU HEREIN EXPRESSLY ACKNOWLEDGE AND AGREE THAT:
<br/><br/>
a) THE USE OF DEZINOW, INC. SERVICES AND SOFTWARE ARE AT THE SOLE RISK BY YOU. OUR SERVICES
AND SOFTWARE SHALL BE PROVIDED ON AN "AS IS" AND/OR "AS AVAILABLE" BASIS. DEZINOW, INC.
AND OUR SUBSIDIARIES, AFFILIATES, OFFICERS, EMPLOYEES, AGENTS, PARTNERS AND LICENSORS
EXPRESSLY DISCLAIM ANY AND ALL WARRANTIES OF ANY KIND WHETHER EXPRESSED OR IMPLIED,
INCLUDING, BUT NOT LIMITED TO ANY IMPLIED WARRANTIES OF TITLE, MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
<br/><br/>
b) DEZINOW, INC. AND OUR SUBSIDIARIES, OFFICERS, EMPLOYEES, AGENTS, PARTNERS AND LICENSORS
MAKE NO SUCH WARRANTIES THAT (i) DEZINOW, INC. SERVICES OR SOFTWARE WILL MEET YOUR
REQUIREMENTS; (ii) DEZINOW, INC. SERVICES OR SOFTWARE SHALL BE UNINTERRUPTED, TIMELY,
SECURE OR ERRORFREE; (iii) THAT SUCH RESULTS WHICH MAY BE OBTAINED FROM THE USE OF THE
DEZINOW, INC. SERVICES OR SOFTWARE WILL BE ACCURATE OR RELIABLE; (iv) QUALITY OF ANY
PRODUCTS, SERVICES, ANY INFORMATION OR OTHER MATERIAL WHICH MAY BE PURCHASED OR
OBTAINED BY YOU THROUGH OUR SERVICES OR SOFTWARE WILL MEET YOUR EXPECTATIONS; AND (v)
THAT ANY SUCH ERRORS CONTAINED IN THE SOFTWARE SHALL BE CORRECTED.
<br/><br/>
c) ANY INFORMATION OR MATERIAL DOWNLOADED OR OTHERWISE OBTAINED BY WAY OF DEZINOW,
INC. SERVICES OR SOFTWARE SHALL BE ACCESSED BY YOUR SOLE DISCRETION AND SOLE RISK, AND AS
<br/><br/>
SUCH YOU SHALL BE SOLELY RESPONSIBLE FOR AND HEREBY WAIVE ANY AND ALL CLAIMS AND CAUSES
OF ACTION WITH RESPECT TO ANY DAMAGE TO YOUR COMPUTER AND/OR INTERNET ACCESS,
DOWNLOADING AND/OR DISPLAYING, OR FOR ANY LOSS OF DATA THAT COULD RESULT FROM THE
DOWNLOAD OF ANY SUCH INFORMATION OR MATERIAL.
<br/><br/>
d) NO ADVICE AND/OR INFORMATION, DESPITE WHETHER WRITTEN OR ORAL, THAT MAY BE OBTAINED
BY YOU FROM DEZINOW, INC. OR BY WAY OF OR FROM OUR SERVICES OR SOFTWARE SHALL CREATE
ANY WARRANTY NOT EXPRESSLY STATED IN THE TOS.
<br/><br/>
e) A SMALL PERCENTAGE OF SOME USERS MAY EXPERIENCE SOME DEGREE OF EPILEPTIC SEIZURE WHEN
EXPOSED TO CERTAIN LIGHT PATTERNS OR BACKGROUNDS THAT MAY BE CONTAINED ON A COMPUTER
SCREEN OR WHILE USING OUR SERVICES. CERTAIN CONDITIONS MAY INDUCE A PREVIOUSLY UNKNOWN
CONDITION OR UNDETECTED EPILEPTIC SYMPTOM IN USERS WHO HAVE SHOWN NO HISTORY OF ANY
PRIOR SEIZURE OR EPILEPSY. SHOULD YOU, ANYONE YOU KNOW OR ANYONE IN YOUR FAMILY HAVE AN
EPILEPTIC CONDITION, PLEASE CONSULT A PHYSICIAN IF YOU EXPERIENCE ANY OF THE FOLLOWING
SYMPTOMS WHILE USING OUR SERVICES: DIZZINESS, ALTERED VISION, EYE OR MUSCLE TWITCHES, LOSS
OF AWARENESS, DISORIENTATION, ANY INVOLUNTARY MOVEMENT, OR CONVULSIONS.
</p></div><div class="question"><h4>
LIMITATION OF LIABILITY</h4><p>

YOU EXPLICITLY ACKNOWLEDGE, UNDERSTAND AND AGREE THAT DEZINOW, INC. AND OUR
SUBSIDIARIES, AFFILIATES, OFFICERS, EMPLOYEES, AGENTS, PARTNERS AND LICENSORS SHALL NOT BE
LIABLE TO YOU FOR ANY PUNITIVE, INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL OR EXEMPLARY
DAMAGES, INCLUDING, BUT NOT LIMITED TO, DAMAGES WHICH MAY BE RELATED TO THE LOSS OF ANY
PROFITS, GOODWILL, USE, DATAAND/OR OTHER INTANGIBLE LOSSES, EVEN THOUGH WE MAY HAVE
BEEN ADVISED OF SUCH POSSIBILITY THAT SAID DAMAGES MAY OCCUR, AND RESULT FROM:
<br/><br/>
a) THE USE OR INABILITY TO USE OUR SERVICE;
<br/><br/>
b) THE COST OF PROCURING SUBSTITUTE GOODS AND SERVICES;
<br/><br/>
c) UNAUTHORIZED ACCESS TO OR THE ALTERATION OF YOUR TRANSMISSIONS AND/OR DATA;
<br/><br/>
d) STATEMENTS OR CONDUCT OF ANY SUCH THIRD PARTY ON OUR SERVICE;
<br/><br/>
e) AND ANY OTHER MATTER WHICH MAY BE RELATED TO OUR SERVICE.
</p></div><div class="question"><h4>
RELEASE</h4><p>

In the event you have a dispute, you agree to release DeziNow, Inc. (and its officers, directors,
employees, agents, parent subsidiaries, affiliates, co-branders, partners and any other third parties)
from claims, demands and damages (actual and consequential) of every kind and nature, known and
unknown, suspected or unsuspected, disclosed and undisclosed, arising out of or in any way connected
to such dispute.
</p></div><div class="question"><h4>
SPECIAL ADMONITION RELATED TO FINANCIAL MATTERS
</h4><p>
Should you intend to create or to join any service, receive or request any such news, messages, alerts or
other information from our Services concerning companies, stock quotes, investments or securities,
please review the above Sections Warranty Disclaimers and Limitations of Liability again. In addition, for
this particular type of information, the phrase "Let the investor beware" is appropriate. DeziNow, Inc.'s
content is provided primarily for informational purposes, and no content that shall be provided or
included in our Services is intended for trading or investing purposes. DeziNow, Inc. and our licensors
shall not be responsible or liable for the accuracy, usefulness or availability of any information
transmitted and/or made available by way of our Services, and shall not be responsible or liable for any
trading and/or investment decisions based on any such information.
</p></div><div class="question"><h4>
EXCLUSION AND LIMITATIONS</h4><p>

THERE ARE SOME JURISDICTIONS WHICH DO NOT ALLOW THE EXCLUSION OF CERTAIN WARRANTIES OR
THE LIMITATION OF EXCLUSION OF LIABILITY FOR INCIDENTAL OR CONSEQUENTIAL DAMAGES.
THEREFORE, SOME OF THE ABOVE LIMITATIONS OF SECTIONS WARRANTY DISCLAIMERS AND
LIMITATION OF LIABILITY MAY NOT APPLY TO YOU.
</p></div><div class="question"><h4>
THIRD PARTY BENEFICIARIES</h4><p>

You herein acknowledge, understand and agree, unless otherwise expressly provided in this TOS, that
there shall be no third-party beneficiaries to this agreement.
</p></div><div class="question"><h4>
NOTICE
</h4><p>
DeziNow, Inc. may furnish you with notices, including those with regards to any changes to the TOS,
including but not limited to email, regular mail, MMS or SMS, text messaging, postings on our website
Services, or other reasonable means currently known or any which may be herein after developed. Any
such notices may not be received if you violate any aspects of the TOS by accessing our Services in an
unauthorized manner. Your acceptance of this TOS constitutes your agreement that you are deemed to
have received any and all notices that would have been delivered had you accessed our Services in an
authorized manner.
</p></div><div class="question"><h4>
TRADEMARK INFORMATION</h4><p>

You herein acknowledge, understand and agree that all of the DeziNow, Inc. trademarks, copyright,
trade name, service marks, and other DeziNow, Inc. logos and any brand features, and/or product and
service names are trademarks and as such, are and shall remain the property of DeziNow, Inc.. You
herein agree not to display and/or use in any manner the DeziNow, Inc. logo or marks without obtaining
DeziNow, Inc.'s prior written consent.
</p></div><div class="question"><h4>
COPYRIGHT OR INTELLECTUAL PROPERTY INFRINGEMENT CLAIMS NOTICE & PROCEDURES</h4><p>

DeziNow, Inc. will always respect the intellectual property of others, and we ask that all of our users do
the same. With regards to appropriate circumstances and at its sole discretion, DeziNow, Inc. may
disable and/or terminate the accounts of any user who violates our TOS and/or infringes the rights of
<br/><br/>
others. If you feel that your work has been duplicated in such a way that would constitute copyright
infringement, or if you believe your intellectual property rights have been otherwise violated, you
should provide to us the following information:
<br/><br/>
a) The electronic or the physical signature of the individual that is authorized on behalf of the owner of
the copyright or other intellectual property interest;
<br/><br/>
b) A description of the copyrighted work or other intellectual property that you believe has been
infringed upon;
<br/><br/>
c) A description of the location of the site which you allege has been infringing upon your work;
<br/><br/>
d) Your physical address, telephone number, and email address;
<br/><br/>
e) A statement, in which you state that the alleged and disputed use of your work is not authorized by
the copyright owner, its agents or the law;
<br/><br/>
f) And finally, a statement, made under penalty of perjury, that the aforementioned information in your
notice is truthful and accurate, and that you are the copyright or intellectual property owner,
representative or agent authorized to act on the copyright or intellectual property owner's behalf.
<br/><br/>
The DeziNow, Inc. Agent for notice of claims of copyright or other intellectual property infringement can
be contacted as follows:
<br/><br/>
Mailing Address: DeziNow, Inc. Attn: Copyright Agent 1255 Treat Blvd Suite 300 Walnut Creek,
California 94597

Telephone: 415-735-7008 Fax: 415-735-7008 Email: customer@dezinow.com
</p></div><div class="question"><h4>
CLOSED CAPTIONING</h4><p>

BE IT KNOWN, that DeziNow, Inc. complies with all applicable Federal Communications Commission
rules and regulations regarding the closed captioning of video content. For more information, please
visit our website at www.DeziNow.com.
</p></div><div class="question"><h4>
GENERAL INFORMATION</h4><p>

ENTIRE AGREEMENT

This TOS constitutes the entire agreement between you and DeziNow, Inc. and shall govern the use of
our Services, superseding any prior version of this TOS between you and us with respect to DeziNow, Inc.
Services. You may also be subject to additional terms and conditions that may apply when you use or
purchase certain other DeziNow, Inc. Services, affiliate Services, third-party content or third-party
software. You affirm that you are of sound mind and body to enter this agreement and are able to
understand it. You hereby waive any defense to the enforceability of its terms and conditions.
<br/><br/>
CHOICE OF LAW AND FORUM
<br/><br/>
It is at the mutual agreement of both you and DeziNow, Inc. with regard to the TOS that the relationship
between the parties shall be governed by the laws of the state of California without regard to its conflict
of law provisions and that any and all claims, causes of action and/or disputes, arising out of or relating
to the TOS, or the relationship between you and DeziNow, Inc., shall be filed within the courts having
jurisdiction within the County of Contra Costa, California or the U.S. District Court located in said state.
You and DeziNow, Inc. agree to submit to the jurisdiction of the courts as previously mentioned, and
agree to waive any and all objections to the exercise of jurisdiction over the parties by such courts and
to venue in such courts.
<br/><br/>
WAIVER AND SEVERABILITY OF TERMS
<br/><br/>
At any time, should DeziNow, Inc. fail to exercise or enforce any right or provision of the TOS, such
failure shall not constitute a waiver of such right or provision. If any provision of this TOS is found by a
court of competent jurisdiction to be invalid, the parties nevertheless agree that the court should
endeavor to give effect to the parties' intentions as reflected in the provision, and the other provisions
of the TOS remain in full force and effect.
<br/><br/>
NO RIGHT OF SURVIVORSHIP NON-TRANSFERABILITY
<br/><br/>
You acknowledge, understand and agree that your account is non-transferable and any rights to your ID
and/or contents within your account shall terminate upon your death. Upon receipt of a copy of a death
certificate, your account may be terminated and all contents therein permanently deleted.
<br/><br/>
STATUTE OF LIMITATIONS
<br/><br/>
You acknowledge, understand and agree that regardless of any statute or law to the contrary, any claim
or action arising out of or related to the use of our Services or the TOS must be filed within 1 year(s)
after said claim or cause of action arose or shall be forever barred.
<br/><br/>
VIOLATIONS
<br/><br/>
Please report any and all violations of this TOS to DeziNow, Inc. as follows:

Mailing Address: DeziNow, Inc. 1255 Treat Blvd Suite 300 Walnut Creek, California 94597 Telephone:
415-735-7008 Fax: 415-735-7008 Email: customer@dezinow.com
</p></div>
					
					</div>
					
					
				</div>
			</div>
		</div>  
	</div>
</section>
@endsection