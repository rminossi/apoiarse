	@extends('web.master.master')
	@section('content')
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
			<div class="container">
			<form action="{{route('enviar-contato')}}" method="POST">
					@csrf
                @isset($site)
					<div class="row">
						<div class="col-md-6">
							<h3 class="section-title">Onde Estamos,</h3>
							<ul class="contact-info">
								<li><i class="icon-location-pin"></i>Torres, RS</li>
                                <li><i class="icon-phone2"></i><a target="_blank" href="https://api.whatsapp.com/send?phone=5551980436739">{{$site->phone}}</a></li>
								<li><i class="icon-mail"></i><a target="_blank" href="mailto:rafaelminossi@gmail.com">{{$site->email}}</a></li>
								<li><i class="icon-instagram"></i><a target="_blank" href="https://www.instagram.com/_sorteios_todo_dia_">www.instagram.com/_sorteios_todo_dia_</a></li>
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
                @endisset
				</form>
			</div>
		</div>
		<!-- END map -->
		@endsection
