<?php
namespace Search\Test\TestCase\Model\Behavior;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Search\Manager;

class ArticlesTable extends Table
{

    public function searchConfiguration()
    {
        $manager = new Manager($this);

        return $manager
            ->value('foo')
            ->like('search', ['filterEmpty' => true])
            ->value('baz')
            ->value('group', ['field' => 'Articles.group']);
    }
}

class CommentsTable extends Table
{

    public function searchConfiguration()
    {
        $manager = new Manager($this);

        return $manager
            ->value('Comments.foo')
            ->like('Comments.search', ['filterEmpty' => true])
            ->value('Comments.baz')
            ->value('Comments.group', ['field' => 'Comments.group']);
    }
}

class SearchBehaviorTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.Search.Articles',
        'core.Comments'
    ];

    /**
     * setup
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        TableRegistry::clear();
        $this->Articles = TableRegistry::get('Articles', [
            'className' => 'Search\Test\TestCase\Model\Behavior\ArticlesTable'
        ]);
        $this->Articles->addBehavior('Search.Search');
        $this->Comments = TableRegistry::get('Comments', [
            'className' => 'Search\Test\TestCase\Model\Behavior\CommentsTable'
        ]);
        $this->Comments->addBehavior('Search.Search');
    }

    /**
     * Test the custom "search" finder
     *
     * @return void
     */
    public function testFinder()
    {
        $queryString = [
            'foo' => 'a',
            'search' => 'b',
            'group' => 'main'
        ];
        $this->assertFalse($this->Articles->isSearch());

        $query = $this->Articles->find('search', ['search' => $queryString]);
        $this->assertEquals(3, $query->clause('where')->count());

        $queryString['search'] = '';
        $query = $this->Articles->find('search', ['search' => $queryString]);
        $this->assertEquals(2, $query->clause('where')->count());

        $queryString['foo'] = '';
        $query = $this->Articles->find('search', ['search' => $queryString]);
        $this->assertEquals(1, $query->clause('where')->count());

        $query = $this->Articles->find('search', [
            'search' => [
                'foo' => 0,
                'search' => 'b',
                'page' => 1
            ]
        ]);
        $this->assertEquals(2, $query->clause('where')->count());
        $this->assertTrue($this->Articles->isSearch());
    }

    /**
     * Test the custom "search" finder
     *
     * @return void
     */
    public function testAliasedFinder()
    {
        $queryString = [
            'Comments' => [
                'foo' => 'a',
                'search' => 'b',
                'group' => 'main'
            ]
        ];

        $query = $this->Comments->find('search', ['search' => $queryString]);
        $this->assertEquals(3, $query->clause('where')->count());

        $queryString['Comments']['search'] = '';
        $query = $this->Comments->find('search', ['search' => $queryString]);
        $this->assertEquals(2, $query->clause('where')->count());

        $queryString['Comments']['foo'] = '';
        $query = $this->Comments->find('search', ['search' => $queryString]);
        $this->assertEquals(1, $query->clause('where')->count());
    }

    /**
     * testFindSearchException
     *
     * @expectedException Exception
     * @expectedExceptionMessage Custom finder "search" expects search arguments to be nested under key "search" in find() options.
     * @return void
     */
    public function testFindSearchException()
    {
        $query = $this->Articles->find('search');
    }

    /**
     * Tests the filterParams method
     *
     * @return void
     */
    public function testFilterParams()
    {
        $result = $this->Articles->filterParams(['foo' => 'bar']);
        $this->assertEquals(['search' => ['foo' => 'bar']], $result);
    }

    /**
     * testSearchManager
     *
     * @return void
     */
    public function testSearchManager()
    {
        $manager = $this->Articles->searchManager();
        $this->assertInstanceOf('\Search\Manager', $manager);
    }
}
