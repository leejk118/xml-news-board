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

        
