{{-- Comments JS --}}
<script type="text/javascript">
	if (baseURL == undefined) var baseURL = '{{ URL::to('') }}';
</script>
<script type="text/javascript" src="{{ Site::js('comments', 'regulus/open-comments') }}"></script>

{{-- Add Comment Form --}}
@include('open-comments::partials.add')

{{-- Comments List --}}
@include('open-comments::templates.list')