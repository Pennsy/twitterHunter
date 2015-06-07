#!/ucr/bin/python

INDEX_DIR = "index"
MAX_RESULT = 100

import sys, os, lucene, json

from java.io import File
from org.apache.lucene.analysis.standard import StandardAnalyzer
from org.apache.lucene.index import DirectoryReader
from org.apache.lucene.queryparser.classic import QueryParser
from org.apache.lucene.store import SimpleFSDirectory
from org.apache.lucene.search import IndexSearcher
from org.apache.lucene.util import Version


def run(command,searchoption,searcher, analyzer):
	
	resultfile = open('./result.txt', 'w')
	searchtype = "text"
	if searchoption == 1:
		searchtype = "user"
	query = QueryParser(Version.LUCENE_CURRENT, searchtype,analyzer).parse(command)
	#scoreDocs = searcher.search(query, MAX_RESULT).scoreDocs
	topDocs = searcher.search(query,MAX_RESULT)
	scoreDocs = topDocs.scoreDocs

	for scoreDoc in scoreDocs:
		doc = searcher.doc(scoreDoc.doc)
		#print "@user:", doc.get("user"), doc.get("text"),"\n","coordinates:",doc.get("geolon"),",", doc.get("geolat")
		result = {'user':doc.get("user")}
		result['text'] = doc.get("text")
		result['lon'] = doc.get("geolon")
		result['lat'] = doc.get("geolat")
		result['id_str'] = doc.get("id_str")
		resultfile.write(json.dumps(result))
		resultfile.write('\n')
	#print "%s total matching documents." % len(scoreDocs)
	resultfile.close()
	return


if __name__ == '__main__':
	lucene.initVM()
	#print 'lucene', lucene.VERSION
	base_dir = '/Users/shinsakairi/PycharmProjects/twitter_data/'
	directory = SimpleFSDirectory(File(os.path.join(base_dir, INDEX_DIR)))
	searcher = IndexSearcher(DirectoryReader.open(directory))
	analyzer = StandardAnalyzer(Version.LUCENE_CURRENT)
	querycontent = sys.argv[1]
	searchoption = sys.argv[2]
	run(querycontent, searchoption, searcher, analyzer)
	#要改，对于所有的查询只维持一个searcher,不然耗时且占内存 Lucene in action 3.1.1
	del searcher


