namespace FamilyTreeSystem.Models.Enums
{
    /// <summary>
    /// 血缘关系类型枚举
    /// </summary>
    public enum RelationshipType
    {
        /// <summary>
        /// 生父
        /// </summary>
        BiologicalFather = 1,
        
        /// <summary>
        /// 养父
        /// </summary>
        AdoptiveFather = 2,
        
        /// <summary>
        /// 义父
        /// </summary>
        GodFather = 5,
        
        /// <summary>
        /// 继父
        /// </summary>
        StepparentFather = 7,
        
        /// <summary>
        /// 生母
        /// </summary>
        BiologicalMother = 3,
        
        /// <summary>
        /// 养母
        /// </summary>
        AdoptiveMother = 4,
        
        /// <summary>
        /// 义母
        /// </summary>
        GodMother = 6,
        
        /// <summary>
        /// 继母
        /// </summary>
        StepparentMother = 8,
        
        /// <summary>
        /// 原配配偶
        /// </summary>
        PrimarySpouse = 5,
        
        /// <summary>
        /// 再婚配偶
        /// </summary>
        SecondarySpouse = 6,
        
        /// <summary>
        /// 长子
        /// </summary>
        EldestSon = 10,
        
        /// <summary>
        /// 次子
        /// </summary>
        SecondSon = 11,
        
        /// <summary>
        /// 三子
        /// </summary>
        ThirdSon = 12,
        
        /// <summary>
        /// 四子
        /// </summary>
        FourthSon = 13,
        
        /// <summary>
        /// 长女
        /// </summary>
        EldestDaughter = 20,
        
        /// <summary>
        /// 次女
        /// </summary>
        SecondDaughter = 21,
        
        /// <summary>
        /// 三女
        /// </summary>
        ThirdDaughter = 22,
        
        /// <summary>
        /// 长兄
        /// </summary>
        EldestBrother = 30,
        
        /// <summary>
        /// 次兄
        /// </summary>
        SecondBrother = 31,
        
        /// <summary>
        /// 三兄
        /// </summary>
        ThirdBrother = 32,
        
        /// <summary>
        /// 四兄
        /// </summary>
        FourthBrother = 33,
        
        /// <summary>
        /// 长姐
        /// </summary>
        EldestSister = 40,
        
        /// <summary>
        /// 次姐
        /// </summary>
        SecondSister = 41,
        
        /// <summary>
        /// 三姐
        /// </summary>
        ThirdSister = 42,
        
        /// <summary>
        /// 四姐
        /// </summary>
        FourthSister = 43,
        
        /// <summary>
        /// 次弟
        /// </summary>
        SecondYoungerBrother = 51,
        
        /// <summary>
        /// 三弟
        /// </summary>
        ThirdYoungerBrother = 52,
        
        /// <summary>
        /// 四弟
        /// </summary>
        FourthYoungerBrother = 53,
        
        /// <summary>
        /// 小弟
        /// </summary>
        YoungestBrother = 54,
        
        /// <summary>
        /// 次妹
        /// </summary>
        SecondYoungerSister = 61,
        
        /// <summary>
        /// 三妹
        /// </summary>
        ThirdYoungerSister = 62,
        
        /// <summary>
        /// 四妹
        /// </summary>
        FourthYoungerSister = 63,
        
        /// <summary>
        /// 小妹
        /// </summary>
        YoungestSister = 64
    }
}