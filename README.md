# XML-NEWS-BOARD PROJECT

## setup
### Laravel project 설치
* `composer create-project --prefer-dist laravel/larvel xml-news-board`
* `php artisan key:generate --ansi`

### Github 연동
* Github에 repository 생성
* `git init`
* `git add .`
* `git commit -m "create laravel project"`
* `git remote add origin https://github.com/leejk118/xml-news-board.git`
* `git push -u origin master`

### Nginx 연동
참고 : https://medium.com/@koswarabilly/laravel-with-nginx-installation-to-set-up-94eae92e2541
* nginx-1.19.0/conf/nginx.conf
    ```
          server {
              listen       7000;
              server_name  localhost;
              root xml-news-board/public/;
      
              location / {
                index  index.html index.htm index.php;
                try_files $uri $uri/ /index.php?$query_string;
              }

              location ~ \.php$ {
                try_files $uri /index.php = 404;
                fastcgi_pass  127.0.0.1:9000;
                fastcgi_index index.php;
                fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
                include fastcgi_params;
              }
     ```
* 프로젝트 실행
    * php7 디렉토리에서 `php-cgi.exe -b 127.0.0.1:9000` 실행
    * nginx 디렉토리에서 `start nginx`
    * 브라우저에서 127.0.0.1:7000 접속해서 확인
        > 실행파일 예시
        ```shell script
          @echo on
          cd c:/nginx-1.19.0/nginx-1.19.0
          start nginx
          cd c:/nginx-1.19.0/nginx-1.19.0/php7 
          php-cgi.exe -b 127.0.0.1:9000
          pause
        ```
    
### MySQL 연동
* .env  파일 설정
    ```
    DB_HOST=127.0.0.1 
    DB_DATABASE=myapp
    DB_USERNAME=jklee
    DB_PASSWORD='{password}'
    ```
- 데이터베이스 만들기
    - 관리자 계정으로 MySQL 접속
        - `mysql -uroot -p`
        - 비밀번호 입력 (MySQL 설치 시 생성한 비밀번호)
    - DB 생성
        - `CREATE DATABASE mydb;`
        - `CREATE USER 'jklee' IDENTIFIED BY '{password}';`
        - `GRANT ALL PRIVILEGES ON mydb.* TO 'jklee';`
        - `FLUSH PRIVILEGES;`
        - `quit`
    - 사용자 계정으로 접속 확인
        - `mysql -ujklee -p`
        - `use mydb`
        
- 마이그레이션 만들기
    - `php artisan make:migration create_articles_table --create=articles`
    - TIMESTAMP_create_articles_table.php
        ```php 
          public function up(){
              Schema::create('articles', function(Blueprint $table){
                    $table->id();
                    $table->string('title');
                    $table->string('subtitle')->nullable();
                    $table->string('content', 10000);
                    $table->date('send_date');
                    $table->string('new_link');
                    $table->timestamps();
              });    
          }   
        ```
    - 마이그레이션 실행
        - `php artisan migrate`
    - 실행 결과 확인
        - `mysql -ujklee -p;`
        - `use mydb;`
        - `DESCRIBE articles;`
    - 롤백
        - `php artisan migrate:rollback`

## XML 데이터 파싱
- 배치 파일 생성
    - `php artisan make:command MakeNews`
        - App\Console\Commands 디렉토리에 생성
    - `php artisan list` 명령 추가된 것 확인
    - MakeNews.php
        ```php 
           protected $signature = `command:make-news`;
        ```
    - `php artisan command:make-news` 실행 시 MakeNews.php의 handle() 메서드가 실행
    
- Article 모델 생성
    - `php artisan make:model Article`
    - fillable 설정
        - ```php 
          protected $fillable = ['title', 'subtitle', 'content', 'send_date', 'news_link'];
          ```
- 정규표현식 사용
    - 연합뉴스 XML의 TaggedBody에서 YNAPHOTO 태그를 이미지 태그로 치환
    

## Article 게시판 생성
-  RESTful 리소스 컨트롤러 생성
    - 컨트롤러 생성
        - `php artisan make:controller ArticlesController --resource`
    - web.php
        ```php
          Route::resource('articles', 'ArticleController');
        ```
    - 라우트 목록 생성 확인
        - `php artisan route:list`

- 부트스트랩 적용
    - `composer require laravel/ui`
    - `php artisan ui bootstrap --auth`
    - `npm install && npm run dev`
    
- Article 뷰 생성
    - resources/views/articles/index.blade.php
        ```php 
      @extends('layouts.app')
      
      @section('content')
          <div class="container">
              <h1>News</h1>
              <hr>
              <ul>
                  @foreach($articles as $article)
                      <li>
                          {{ $article->title }}
                      </li>
                  @endforeach
              </ul>
      
              @if($articles->count())
                  <div class="text-center">
                      {!! $articles->render() !!}
                  </div>
              @endif
          </div>
      @stop

           
        ```

    - ArticlesController.php
        ```php 
      public function index()
      {
          $articles = \App\Article::latest()->paginate(10);
  
          return view('article.index', compact('articles'));
      }
        ```                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
        > 커스텀 페이징 추후 구현 예정

- 게시글 조회기능
    - ArticlesController.php
        ```php 
      public function show($id)
      {
          $article = \App\Article::find($id);
  
          return view('articles.show', compact('article'));
      }
        ```
      
    - show.blade.php
        ```php 
        @extends('layouts.app')
        
        @section('content')
            <div class="container">
                <h1>{{ $article->title }}</h1>
                <hr>
                <h3>{{ $article->subtitle }}</h3>
                <p>작성일 : {{ $article->send_date }}</p>
                <p>원본링크 : <a href="{{ $article->news_link }}" target="_blank">{{ $article->news_link }}</a></p>
                <br><br>
                {!!  $article->content  !!}
            </div>
        @stop
        ```
      
    - index.blade.php
      ```php 
      <table>
          <tr>
              <th>기사 제목</th>
              <th>등록일</th>
              <th>조회수 (예정)</th>
          </tr>
          @foreach($articles as $article)
              <tr>
                  <td>
                      <a href="articles/{{ $article->id }}">
                          (미리보기)
                          {{ $article->title }}
                      </a>
                  </td>
                  <td>
                      {{ $article->send_date }}
                  </td>
              </tr>
          @endforeach
      </table>  
      ```
    
## 미리보기 이미지 추가하기
* Article 열 추가 마이그레이션
    - `php artisan make:migration add_preview_img_to_articles_table --table=articles`
- TIMESTAMP_add_preview_img_to_articles_table.php
    ```php 
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
        $table->string('preview_img')->nullable();
    });
    public function down()
    {
      Schema::table('articles', function (Blueprint $table) {
          $table->dropColumn('preview_img');
      });
    }      
    ```
- 마이그레이션 실행
    - `php artisan migrate`

- 열 추가 했을 경우 fillable 추가 설정 (중요)
- MakeNews.php 수정

## 미리보기 내용 기능 구현
* 열 추가 마이그레이션
    - `php artisan make:migration add_preview_content_to_articles_table --table=articles`
    - ```php 
        public function up()
            {
                Schema::table('articles', function (Blueprint $table) {
                    $table->string('preview_content', 500);
                });
            }
      
            public function down()
            {
                Schema::table('articles', function (Blueprint $table) {
                    $table->dropColumn('preview_content');
                });
            }
        ```
    - 마이그레이션 실행
        - `php artisan migrate`
* MakeNews.php 수정
    - ```php 
        $body = str_replace("\n", ' ', $xml->NewsContent->Body);
                    $preview_content = iconv_substr($body, 0, 100, "UTF-8");
        ```
- Article 모델에 fillable 추가
- articles/index.blade.php 적절하게 수정
               
    
> CHECK : 한글 문자열 파싱 시 substr vs. iconv_substr vs. mb_substr
> TODO : hover 시 이미지 투명하게?, 
    
    
          
## 검색 기능 추가
   
* articles/index.blade.php
    - 인덱스 페이지 밑 부분에 카테고리, 검색 영역, 검색 버튼 추가
    ```php 
    <div class="divCenter">
        <form action="{{ route('articles.index') }}" metod="get">
            <select class="form-control " style="width: 200px; display: inline-block" name="category">
                <option value="both">제목 + 본문</option>
                <option value="title">제목</option>
                <option value="content">본문</option>
            </select>
            <input type="text" name="q" class="form-control searchForm" placeholder="기사 검색" />
            <button type="submit" class="btn btn-primary">검색</button>
        </form>
    </div>
    ```
   
- ArticleController.php
    - 컨트롤러에 로직 추가
    > 리팩토링 필요
    ```php 
    public function index(Request $request)
    {
        if ($request->input('q') != null){
            $keyword = $request->q;
            $category = $request->category;

        if ($category == 'both') {
            $articles = \App\Article::where('title', 'like', '%' . $keyword  . '%')
                                        ->orWhere('content', 'like', '%' . $keyword  . '%')
                                        ->paginate(10);
        }
        else {
            $articles = \App\Article::where($category, 'like', '%' . $keyword  . '%')
                                        ->paginate(10);
        }

            $articles->withQueryString()->links();
        }
        else {
            $articles = \App\Article::latest()->paginate(10);
        }

        return view('articles.index', compact('articles'));
    }
    ```  

## 삭제 기능 추가
- 일괄 삭제 버튼 생성
    - index.blade.php
        ```php 
            <button id="deleteAll" onclick="button_click()">일괄삭제</button>
        ```
        ```php 
            @section('script')
                <meta name="csrf-token" content="{{ csrf_token() }}">
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
                <script>
                    function button_click(){
                        var target = $("input[type='checkbox']").filter(':checked');
            
                        var targetList = [];
                        for (var i = 0; i < target.length; ++i){
                            targetList.push(target[i].value);
                        }
            
                        if (confirm("전부 삭제?")){
                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                url : '{{ route('articles.destroys') }}',
                                type : 'POST',
                                data : JSON.stringify({data : targetList}),
                                success : function () {
                                    alert("삭제 성공");
                                    window.location.href = '{{ route('articles.index') }}';
                                }
                            });
                        }
            
            
                    }
                </script>
            @stop
        ```
- route 등록
    - POST articles/destroy 방식? 
    - web.php
        ```php
        Route::post('articles/destroy', [
            'as' => 'articles.destroys',
            'uses' => 'ArticleController@destroys'
        ]);
        ```
- 컨트롤러 수정
    - ArticleController.php
        ```php 
        public function destroys(Request $request){
            $list = json_decode($request->getContent(), true);
    
            foreach ($list['data'] as $id) {
                \App\Article::destroy($id);
            }
    
            return redirect(route('articles.index'));
        }
        ```         

> 리팩토링 필요


              
## 조회수 기능 추가
- 마이그레이션 추가
    - `php artisan make:migration add_view_count_to_articles_table --table=articles`
    - `php artisan migrate`
    - TIMESTAMP_add_view_count_to_articles_table.php
        ```php 
          public function up()
          {
              Schema::table('articles', function (Blueprint $table) {
                  $table->integer('view_count')->default(0);
              });
          }

          public function down()
          {
              Schema::table('articles', function (Blueprint $table) {
                  $table->dropColumn('view_count');
              });
          }
        ```       
      
- ArticleController.php
    ```php 
    public function show($id)
    {
        $article = \App\Article::find($id);

        $article->view_count += 1;
        $article->save();

        return view('articles.show', compact('article'));
    }
    ```
   
> 조회수 조작 방지 기능?   
         
## 수정 기능 추가
- 네이버 스마트 에디터 적용
    - 네이버 깃허브 등에서 받은 nse_files.zip 파일을 압축풀고 public 디렉토리에 복사
    - 주의) js 폴더에 smarteditor2.js 파일 있는지 확인
    - 적용하려는 파일에 다음 코드 추가
        - ```php 
          <script type="text/javascript" src="/se2/js/service/HuskyEZCreator.js" charset="utf-8"></script>  
          
          <script type="text/javascript">
                var oEditors = [];
                nhn.husky.EZCreator.createInIFrame({
                    oAppRef: oEditors,
                    elPlaceHolder: "ir1",
                    sSkinURI: "/se2/SmartEditor2Skin.html",
                    fCreator: "createSEditor2",
                });
            </script>
            ```
        
- edit.blade.php
    - ```php 
        <form action="{{ route('articles.update', $article->id) }}" method="POST">
            {!! csrf_field() !!}
            <input type="hidden" name="_method" value="PUT">
            제목
            <input type="text" class="input-group" name="title" value="{{ $article->title }}"><br><br>

            <textarea name="ir1" id="ir1" rows="10" cols="100" style="width:766px; height:412px;">
                {{ $article->content }}
            </textarea>
            <input type="submit" value="저장하기" onclick="submitContents(this)"/>
        </form>
      
        ... 
      
        <script>
            function submitContents(e){
                oEditors.getById["ir1"].exec("UPDATE_CONTENTS_FIELD", []);
    
                try {
                    e.form.submit();
                }
                catch(ex){
                }
            }
        </script>
        ```
      
- ArticleController.php
    - ```php 
          public function edit($id)
          {
              $article = \App\Article::find($id);
      
              return view('articles.edit', compact('article'));
          }
      
          public function update(Request $request, $id)
          {
              $content = $request->ir1;
              $previewContent = iconv_substr(preg_replace("/<(.+?)>/", "", $content), 0, 100, "UTF-8");
      
              \App\Article::where('id', $id)
                          ->update(['title' => $request->title,
                                  'content' => $content,
                                  'preview_content' => $previewContent]);
      
              return redirect(route('articles.index'));
          }
        ```      

## 로그인에 따른 수정/삭제 기능 추가
* 라라벨 auth 기능 사용
    - `composer require laravel/ui --dev`
    - `php artisan ui bootstrap --auth`
- Index 페이지에서 로그인 시 일괄삭제/개별삭제/수정 기능 구현
    - admin 부분 @auth @endauth로 처리


## 수정/삭제/조회 리팩토링 - 예외사항 
- n번째 페이지에서 삭제 시 n번째 페이지로 이동
    - 개별 삭제 : back() 메서드 사용
        ```php 
          public function destroy($id)
          {
              \App\Article::destroy($id);
      
              return back();
          }
        ```
    - 일괄 삭제 : 리로드 방식으로 처리
        ```php 
        success : function () {
            alert("삭제 성공");
            window.location.reload();
        }
        ```
      
- 컨텐츠 수정 시 수정된 컨텐츠 show 페이지로 이동
    - 라우트 처리
        ```php 
        return view('articles.show', ['article' => \App\Article::find($id)]);
        ```
      
- 마지막 페이지에 1개 남은 컨텐츠 삭제 시 페이지 이동 / 마지막 페이지 일괄 삭제 시 ?
    > 페이징 넘어갈 경우 처리 (총 40페이지인경우 page=41을 줄 경우)
    - $article이 LengthAwarePaginator 객체이므로, lastPage값과 비교하여 넘을 시 초기 페이지로 리다이렉트
     ```php 
        if ($request->page > $articles->lastPage()){
            return redirect(route('articles.index'));
        }
    ```
  
- 뉴스 detail 페이지에서 삭제 시 이슈 & 뉴스 detail 페이지에서 수정 후 목록으로 이동 시 이슈
    - queryString 유지하며 페이지 이동 처리
    -  `<a href="{{ route('articles.show', [$article->id, $_SERVER['QUERY_STRING']]) }}" class="text-dark" >`
    - ```html 
        <form action="{{ route('articles.update', $article->id) }}" method="POST" onsubmit="return submitContents(this);">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="queryString" value="{{ $_SERVER['QUERY_STRING'] }}">
        ```
      
~~- 검색 후 수정/삭제 기능 구현(마찬가지로 삭제 시 해당 페이지, 수정 시 해당 detail 페이지)~~
- 로그인/로그아웃 시 현재 페이지 유지(수정/삭제 아닌 경우)          
~~- 뉴스 detail 페이지에서 수정/삭제/목록으로 기능 구현 - 비로그인 시에는 목록으로 만 보이게~~
- old 기능 추가



- ArticlesController@index 리팩토링
    - before
        ```php 
        if ($request->input('q') != null){
            $keyword = $request->q;
            $category = $request->category;

            if ($category == 'both') {
                $articles = \App\Article::orWhere('title', 'like', '%' . $keyword  . '%')
                                            ->orWhere('content', 'like', '%' . $keyword  . '%')
                                            ->paginate(10);
            }
            else {
                $articles = \App\Article::orWhere($category, 'like', '%' . $keyword  . '%')
                                            ->paginate(10);
            }

            $articles->withQueryString()->links();
        }
        else {
            $articles = \App\Article::orderBy('id', 'desc')->paginate(10);
        }
        ```
    - after
        ```php 
        $articles = \App\Article::where(function($query) use ($request) {
            if ($request->q != null){
                if ($request->category == 'both'){
                    $query->orWhere('title', 'like', '%' . $request->q  . '%');
                    $query->orWhere('content', 'like', '%' . $request->q  . '%');
                }
                else {
                    $query->orWhere($request->category, 'like', '%' . $request->q  . '%');
                }
            }
        })->orderBy('id', 'desc')->paginate(10);

        $articles->appends(request()->query())->links();
        ```       
    - 참조 
        - https://laravel.io/forum/12-24-2014-eloquent-dynamic-orwhere-query
        - https://github.com/laravel/framework/issues/19441


## 예외처리
- 존재하지 않는 id로 접근
    > articles/101010 등
    - find() -> findorfail($id)로 수정
    - app/Exception/Handler.php
        ```php 
        if (app()->environment('local')){
            if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException){
                return response(view('errors.notice', [
                    'title' => '찾을 수 없습니다.',
                    'description' => '죄송합니다! 요청하신 페이지가 없습니다.'
                ]), 404);
            }
        }
        ```
- errors/notice.blade.php
    ```php 
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8">
            <title>{{ $title }}</title>
            <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
            <style>
                * { line-height: 1.5; margin: 0; }
                html { color : #888; font-family: Sans-serif; text-align: center; }
                body { left:50%; margin : -43px 0 0 -150px; position: absolute; top:50%; width: 300px; }
                h1 { color : #555; font-size: 2em; font-weight: 400; }
                p {line-height: 1.2;}
                @media only screen and (max-width: 400px) {
                    body { margin: 10px auto; position: static; width: 95%; }
                    h1 { font-size: 1.5em;}
                }
                a {
                    text-decoration: none;
                    color: black;
                    font-size: 15px;
                }
            </style>
        </head>
        <body>
            <h1>{{ $title }}</h1>
            <p> {{ $description }}</p>
            <br>
        <a href="/">
            <button class="btn btn-primary">홈으로 이동</button>
        </a>
        </body>
    </html>
    ```                        

## 부가기능
- 전체선택/해제 기능 추가
    - 참고 : https://hellogk.tistory.com/5
    - index.blade.php
        ```php 
        <button id="selectBox" onclick="selectAll()" class="btn btn-secondary">전체선택</button>
        ...
        function selectAll(){
            var current = $("#selectBox").html();
            var btnValue = (current == "전체선택") ? "선택해제" : "전체선택";
            var checked = (current == "전체선택") ? true : false;
    
            $("input[type='checkbox']").prop("checked", checked);
            $("#selectBox").html(btnValue);
        }
        ```

## 기타 
- php-cs-fixer
    - PSR2 문법 적용
    - 설치
        - `composer global require friendsofphp/php-cs-fixer`
    - 실행(파일 or 디렉토리)   
        - `php-cs-fixer fix app`
        - app 디렉토리에 있는 php 파일들 수정됨
        

## 인증 처리
- <이슈> 로그인 하지 않아도 url에 articles/{id}/edit로 접근할 지 수정가능
    - ArticleController에 auth 미들웨어 추가
        ```php 
            public function __construct()
            {
                $this->middleware('auth', ['except' => ['index', 'show']]);
            }
        ```
      - auth 미들웨어가 GET /login으로 리디렉션한다. /login이 없을 경우 에러 발생


## 유효성 검사
- form requests 생성
    - `php artisan make:request ArticleRequest`
- ArticleRequest.php
    - ```php 
       public function rules()
        {
            return [
                'title' => 'required | max:100',
                'subtitle' => 'max:150',
                'news_link' => 'required | max:100',
                'content' => 'required | max:7000',
            ];
        }
        ```
- ArticleController.php
    - ```php 
        public function update(\App\Http\Requests\ArticleRequest $request, \App\Article $article)
        {
            $previewContent = iconv_substr(preg_replace("/<(.+?)>/", "",
                                                $request->all()['content']), 0, 100, "UTF-8");
            $request->merge(['preview_content' => $previewContent]);
    
            $article->update($request->all());
    
            return redirect(route('articles.show', [$article->id, $request->queryString]));
        }
      ```          

## 어제의 주요뉴스 기능
- 마이그레이션 생성
    - `php artisan make:migration create_news_histories_table --create=news_histories`
    - `php artisan migrate`
    - create_news_histories_table.php
        ```php 
        public function up()
        {
            Schema::create('news_histories', function (Blueprint $table) {
                $table->id();
                $table->date('send_date');
                $table->unsignedBigInteger('article_id');
                $table->integer('view_count');
        
                $table->foreign('article_id')->references('id')
                    ->on('articles');
            });
        }
        ```
- 모델
    ```php
   class NewsHistory extends Model
   {
       public $timestamps = false;
   
       protected $fillable = ['send_date', 'article_id', 'view_count'];
   
       public function article()
       {
           return $this->belongsTo(Article::class);
       }
   }
    ```      

- 뷰 컴포저(view composer)
    - 컨트롤러에서 뷰를 반환할 때마다 태그 목록을 변수에 담아 넘기는 것은 깔끔하지 않음
    - AppServiceProvider.php
        ```php 
        public function boot()
        {
            view()->composer("*", function($view){
                $newsHistories = \Cache::rememberForever('newsHistories.list', function(){
                    return \App\NewsHistory::where('send_date', '=', '2020-07-28')->get();
                });
    
                $view->with(compact('newsHistories'));
            });
        }
        ```
    - 어제의 주요뉴스를 하루동안 유지되기 때문에 하루가 지나지 않는 이상 변하지 않는다.
    - 데이터 목록에 변화가 있을 경우 캐시 초기화 명령을 실행한다.
        - `php artisan cache:clear`
        
              


## 테스트


## 기타
- 라라벨 request 파라미터 변경
    - https://stackoverflow.com/questions/36812476/how-to-change-value-of-a-request-parameter-in-laravel

## Custom Register & Login 기능
- web.php
    ```php
    Route::get('auth/register', [
        'as' => 'users.create',
        'uses' => 'UserController@create'
    ]);
    Route::post('auth/register', [
        'as' => 'users.store',
        'uses' => 'UserController@store'
    ]);
    Route::get('auth/confirm/{code}', [
        'as' => 'users.confirm',
        'uses' => 'UserController@confirm'
    ])->where('code', '');
    
    
    Route::get('auth/login', [
        'as' => 'sessions.create',
        'uses' => 'SessionController@create'
    ]);
    Route::post('auth/login', [
        'as' => 'sessions.store',
        'uses' => 'SessionController@store'
    ]);
    Route::get('auth/logout', [
        'as' => 'sessions.destroy',
        'uses' => 'SessionController@destroy'
    ]);
    ```


## Article DB 최적화
- doctrine/dbal 의존성 설치
- migration 파일 만든 후 실행



### TODO
- Major
    - makenews.php 리팩토링
    - 유효성 검사
    - 예외처리
    - 테스트코드
    - DB 최적화
- Minor
    - 검색 old, 수정 old
    - 페이징
    - 조회수
    - 로그인
    - restful api 구현
- Additional
    - xml파일 적재과정
    - news show page 꾸미기
    - 어제의 주요뉴스
    - n개씩 보기
    - 조회수 or 일자별 정렬?
    - FULLTEXT search
    - 스마트 에디터 이미지 처리 및 미리보기    
    - 좋아요 기능
    - 태그 기능?
    - 최근 본or 즐겨찾기한 뉴스 기능

## TOKNOW
> why -> how

>line by line

* web
    - middleware
    - session
    - cookie
    - service & repository
    - **exception**
    - **validation**
    - **tdd**

* php
    - autoload
    - trait
    - namespace 와 use https://edykim.com/ko/post/php-namespace/
    - mixed
    - closure

* laravel
    - ioc
    - service container 
    - service provider
    - facade
    - local scope

#### 로그인 상태로 회원가입 or 메일 인증 접속 시 이슈
- https://wiki.modernpug.org/display/LAR/questions/14033275/middleware-guest%EC%97%90-%EA%B1%B8%EB%A6%AC%EB%A9%B4-redirect%EB%90%98%EB%8A%94-%EA%B3%B3-%EC%84%A4%EC%A0%95%ED%95%98%EB%8A%94%EB%B2%95
- http kernel에서 guest alias 설정된 미들웨어인 RedirectIfAuthenticated 파일 수정 


#### 라우트 필요 메서드만 지정
- https://anko3899.tistory.com/359
- except or only

#### 웹 예외사항 예시
- 페이징 
    - 잘못된 페이징 요청 (ex. page=-1, page=99999)
- 로그인 / 비로그인 처리
- 비로그인 시 edit (url 입력)
- 로그인 상태에서 회원가입


    
