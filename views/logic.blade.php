@foreach ($articles as $article)
  <section>
    @if (!empty($article['title']))
      <h2>{{$article['title']}}</h2>
    @endif
    @if (!empty($article['description']))
      <p>{{$article['description']}}</p>
    @endif
  </section>
@endforeach
