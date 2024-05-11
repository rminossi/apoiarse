	@extends('web.master.master')
	@section('content')
        <style>
            .ajax_response {
                z-index: 6;
                position: fixed;
                padding: 20px 20px 0 0;
                bottom: 0;
                right: 0;
                width: 300px;
                max-width: 100%;
            }
            .ajax_response .message {
                background: #333333;
                font-size: 1.5em;
                font-weight: 400;
                border-radius: 4px;
                color: #ffffff;
                overflow: hidden;
                display: flex;
                align-items: center;
                width: 100%;
                padding: 15px 15px 20px 15px;
                margin-bottom: 15px;
                position: relative;
                cursor: pointer;
            }
            .ajax_response .message:before {
                flex-basis: 0%;
                margin: 50px 15px 0 0 !important;
                font-size: 2.4em;
                color: rgba(0, 0, 0, 0.5);
            }
            .ajax_response .message.success {
                background: #36BA9B;
            }
            .ajax_response .message.info {
                background: #39AED9;
            }
            .ajax_response .message.warning {
                background: #F5B946;
            }
            .ajax_response .message.error {
                background: #D94352;
            }
            .ajax_response .message_time {
                content: "";
                position: absolute;
                left: 0;
                bottom: 0;
                width: 4%;
                height: 5px;
                background: rgba(0, 0, 0, 0.5);
            }

            @media (max-width: 30em) {
                .ajax_response {
                    width: 100%;
                    padding: 20px 20px 0 20px;
                }
            }
        </style>
		<div class="fh5co-hero fh5co-hero-2">
			<div class="fh5co-overlay"></div>
			<div class="fh5co-cover fh5co-cover_2 text-center" data-stellar-background-ratio="0.5" style="background-image: url('{{asset('assets/frontend/images/apoiarse.png')}}');">
				<div class="desc animate-box">
					<h2><strong>Fale</strong> conosco</h2>
					<span>Preencha o formulário abaixo, nos mande uma mensagem,<br /> e nos siga nas redes sociais.</span>
				</div>
			</div>
		</div>
		<!-- end:header-top -->

		<div id="fh5co-contact" class="animate-box">
            <div class="ajax_response"></div>
			<div class="container">
			<form name="contact" action="{{route('enviar-contato')}}" method="POST">
					@csrf
					<div class="row">
						<div class="col-md-6">
							<h3 class="section-title">Onde Estamos,</h3>
							<ul class="contact-info">
								<li><i class="icon-location-pin"></i>Torres, RS</li>
                                <li><i class="icon-phone2"></i><a target="_blank" href="https://api.whatsapp.com/send?phone=5551980436739">(51) 98043-6739 (WhatsApp)</a></li>
								<li><i class="icon-mail"></i><a target="_blank" href="mailto:contato@apoiar-se.online">contato@apoiar-se.online</a></li>
								<li><i class="icon-instagram"></i><a target="_blank" href="https://www.instagram.com/apoiarse.online">www.instagram.com/apoiarse.online</a></li>
							</ul>
						</div>
						<div class="col-md-6">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<input type="text" name="fullname" class="form-control" placeholder="Nome">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<input type="text" name="email" class="form-control" placeholder="Email">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<input type="text" name="phone" class="form-control" placeholder="Telefone">
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group">
										<textarea name="message" class="form-control" id="message" cols="30" rows="7" placeholder="Mensagem"></textarea>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group">
										<input type="submit" value="Enviar" class="btn btn-primary">
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		<!-- END map -->
        <script>
            $('form[name="contact"]').submit(function (event) {
                event.preventDefault();
                const form = $(this);
                const action = form.attr('action');
                const fullname = form.find('input[name="fullname"]').val();
                const email = form.find('input[name="email"]').val();
                const phone = form.find('input[name="phone"]').val();
                const message = form.find('textarea[name="message"]').val();
                $.ajax({
                    url: action,
                    type: 'POST',
                    data: {fullname: fullname, email: email, phone: phone, message: message},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        //reset form
                        form[0].reset();
                        if(response.message) {
                            ajaxMessage(response.message, 3)
                        }
                        if(response.redirect) {
                            window.location.href = response.redirect;
                        }
                    },
                    dataType: 'json'
                });
            })



            var ajaxResponseBaseTime = 3;
            function ajaxMessage(message, time) {
                var ajaxMessage = $(message);

                ajaxMessage.append("<div class='message_time'></div>");
                ajaxMessage.find(".message_time").animate({"width": "100%"}, time * 1000, function () {
                    $(this).parents(".message").fadeOut(200);
                });

                $(".ajax_response").append(ajaxMessage);
            }
        </script>
		@endsection
