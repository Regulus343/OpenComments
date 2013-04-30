{{-- Load jQuery --}}
@if (Config::get('open-comments::loadJquery'))

	<script type="text/javascript" src="http://code.jquery.com/jquery.min.js"></script>

@endif

{{-- Load Bootstrap CSS & JS --}}
@if (Config::get('open-comments::loadBootstrap'))

	<link type="text/css" rel="stylesheet" href="{{ Site::css('bootstrap', 'regulus/open-comments') }}" />
	<script type="text/javascript" src="{{ Site::js('bootstrap.min', 'regulus/open-comments') }}"></script>

@endif

{{-- Load Boxy --}}
@if (Config::get('open-comments::loadBoxy'))

	<link type="text/css" rel="stylesheet" href="{{ Site::css('boxy', 'regulus/open-comments') }}" />
	<script type="text/javascript" src="{{ Site::js('jquery.boxy', 'regulus/open-comments') }}"></script>

@endif

{{-- Comments CSS --}}
<link type="text/css" rel="stylesheet" href="{{ Site::css('comments', 'regulus/open-comments') }}" />

{{-- Comments JS --}}
<script type="text/javascript">
	if (baseURL == undefined) var baseURL = "{{ URL::to('') }}";

	var commentLabels   = {{ json_encode(Lang::get('open-comments::labels')) }};
	var commentMessages = {{ json_encode(Lang::get('open-comments::messages')) }};

	@if (!is_null(Site::get('contentID')) && !is_null(Site::get('contentType')))
		var contentID   = "{{ Site::get('contentID') }}";
		var contentType = "{{ Site::get('contentType') }}";
	@else
		if (contentID == undefined)   var contentID   = 0;
		if (contentType == undefined) var contentType = "";
	@endif
</script>

<script type="text/javascript" src="{{ Site::js('wysihtml5', 'regulus/open-comments') }}"></script>
<script type="text/javascript" src="{{ Site::js('wysihtml5-parser-rules', 'regulus/open-comments') }}"></script>

<script type="text/javascript" src="{{ Site::js('comments', 'regulus/open-comments') }}"></script>

{{-- Add Comment Form --}}
@include(Config::get('open-comments::viewsLocation').'partials.add')

{{-- Message --}}
<div id="message-comments">
	<div class="message info hidden"></div>
</div>

{{-- Ajax Loading Image --}}
<div class="loading" id="loading-comments"></div>

{{-- Top Pagination --}}
<ul class="comments-pagination hidden"></ul>

{{-- Comments List --}}
<ul id="comments" class="hidden"></ul>

{{-- JS Template for Comments --}}
@include(Config::get('open-comments::viewsLocation').'templates.list')

{{-- Bottom Pagination --}}
<ul class="comments-pagination hidden"></ul>