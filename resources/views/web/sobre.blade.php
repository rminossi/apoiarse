	@extends('web.master.master')
	@section('content')
		<div id="fh5co-contact" class="animate-box pb-0">
			<div class="container" style="margin-top: 30px;margin-bottom: 30px">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2 text-center heading-section">
                        <h3>Sobre nós</h3>
                        <p>Conheça um pouco mais sobre a Apoiar-se.</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 my-2">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="fh5co-feature animate-box">
                                    <div class="fh5co-icon">
                                        <i class="ti-home"></i>
                                    </div>
                                    <div class="fh5co-text">
                                        <h3 class="font-weight-bold">Quem Somos</h3>
                                        <p>A Apoiar-se é uma iniciativa sem fins lucrativos, com o ideal de fazer vaquinhas e campanhas de arrecadação sem cobrança de taxas como em outras plataformas, é uma plataforma voluntária.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fh5co-feature animate-box">
                                    <div class="fh5co-icon">
                                        <i class="ti-briefcase"></i>
                                    </div>
                                    <div class="fh5co-text">
                                        <h3 class="font-weight-bold">Nossos Valores</h3>
                                        <p>Desde a idealização, a Apoiar-se tem como valores:<br>
                                            <ul>
                                                <li>Respeito</li>
                                                <li>Comprometimento</li>
                                                <li>Transparencia</li>
                                                <li>Colaboração</li>
                                            </ul>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <div class="fh5co-feature animate-box">
                                    <div class="fh5co-icon">
                                        <i class="ti-folder"></i>
                                    </div>
                                    <div class="fh5co-text heading-section mt-3">
                                        <h3>Perguntas frequentes</h3>
                                        <hr>
                                        <h2 class="mt-3 font-weight-bold">Tarifas e prazos</h2>
                                        <p class="font-size-sm">A Apoiar-se possui realmente as menores tarifas do mercado, por se tratar de uma iniciativa sem fins lucrativos (nenhum centavo da sua campanha fica com a gente :) ), que visa diminuir ao máximo a perda de quem precisa de apoio ao usar recursos tecnológicos para obter ajuda, não cobramos taxas para a manutenção da plataforma, somente as taxas cobradas por adminitradoras de pagamento para fazermos as operações financeiras.</p>
                                        <p class="font-size-sm">As taxas são as seguintes:</p>
                                        <ul style="list-style: none">
                                            <li>Doação via PIX: 0,99% ou R$ 0,99 (o que for mais barato)</li>
                                            <li>Doação via Cartão de Crédito: 2,99% + R$ 0,49</li>
                                            <li>Saque: 0%</li>
                                        </ul>
                                        <p class="mt-2">O prazo para pagamento após saque da campanha é de até 24h.</p>
                                        <hr>
                                        <h2 class="mt-3 font-weight-bold">Como a plataforma se mantêm?</h2>
                                        <p class="font-size-sm">A Apoiar-se não possui fins lucrativos, então para a manutenção e pagamentos de custos operacionais, os valores são pagos pelo proprietário do sistema e/ou através de apoio de parceiros.</p>
                                        <hr>
                                        <h2 class="mt-3 font-weight-bold">De onde vem o projeto?</h2>
                                        <p class="font-size-sm">O projeto foi desenvolvido em um momento de calamidade pública, onde se estavam sendo criadas muitas campanhas de arrecadação de recursos para instituiçoes e pessoas que precisavam de ajuda, porém foi observado que o custo das operações poderia ser reduzido, afim de aumentar a arrecadação de quem precisa.</p>
                                        <hr>
                                        <h2 class="mt-3 font-weight-bold">Como funciona?</h2>
                                        <p class="font-size-sm">Os usuários devem se cadastrar para poderem criar suas campanhas ou contribuirem para campanhas existentes. A cada contribuição, o valor do apoio fica disponível na conta da plataforma, disponível para saque pelo criador da campanha.</p>
                                        <hr>
                                        <h2 class="mt-3 font-weight-bold">Porque a chave Pix não é de uma empresa?</h2>
                                        <p class="font-size-sm">Como o projeto foi desenvolvido de forma emergencial, não foi possível ainda criar um CNPJ e uma conta para a plataforma, então o dinheiro estará disponível através de uma conta do sistema de pagamento, ou do criador da plataforma (Rafael Carlos Minossi Nahas) por exigência do sistema de pagamentos. A obtenção do CNPJ está sendo providenciada.</p>
                                        <hr>
                                        <h2 class="mt-3 font-weight-bold">Como vou saber se o valor realmente foi contabilizado na campanha?</h2>
                                        <p class="font-size-sm">Na página da campanha, existe uma sessão de contribuições, onde é possível auditar se o valor foi contabilizado, além do criador da campanha ter a lista de contribuidores no painel e do contribuidor ter a lista de campanhas que ajudou também no painel.</p>
                                        <hr>
                                        <p class="font-size-sm mt-4">Ficou com duvidas? Acesse formulário de contato, clicando <a href="{{ route('web.contato') }}">aqui</a>.</p>
                                    </div>
                                </div>
                            </div>

                    </div>
                </div>
			</div>
		</div>
		<!-- END map -->
		@endsection
